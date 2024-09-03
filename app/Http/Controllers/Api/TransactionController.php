<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\PrintReceiptRequest;
use App\Http\Requests\Transaction\SendReceiptRequest;
use App\Http\Requests\Transaction\UpdateRequest;
use App\Http\Resources\DataCollectionResource;
use App\Mail\TransactionEmailReceiptMail;
use App\Mail\TransactionReceiptMail;
use App\Models\DataCollection;
use App\Models\MailTemplate;
use App\Modules\PrintNode\src\Models\PrintJob;
use App\Services\PdfService;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransactionController extends Controller
{
    public function update(UpdateRequest $request, int $transactionId): DataCollectionResource
    {
        $transaction = DataCollection::findOrFail($transactionId);

        $transaction->update($request->validated());

        return DataCollectionResource::make($transaction);
    }

    public function sendReceipt(SendReceiptRequest $request): bool
    {
        $transaction = DataCollection::findOrFail($request->validated('id'));

        /** @var MailTemplate $template */
        $template = MailTemplate::query()
            ->where('code', 'transaction_email_receipt')
            ->where('mailable', TransactionEmailReceiptMail::class)
            ->first();

        $products = $this->getProducts($transaction);

        $email = new TransactionEmailReceiptMail($template, [
            'transaction' => [
                'id' => $transaction->id,
                'subtotal' => $transaction->total_sold_price,
                'total' => $transaction->total_sold_price,
                'shipping' => 0,
                'tax' => 0,
                'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
            ],
            'shipping_address' => $transaction->shippingAddress->toArray(),
            'billing_address' => $transaction->billingAddress->toArray(),
            'products' => $products,
        ]);

        Mail::to($transaction->shippingAddress->email)->send($email);

        return true;
    }

    public function printReceipt(PrintReceiptRequest $request): bool
    {
        $transaction = DataCollection::findOrFail($request->validated('id'));

        /** @var MailTemplate $template */
        $template = MailTemplate::query()
            ->where('code', 'transaction_receipt')
            ->where('mailable', TransactionReceiptMail::class)
            ->first();

        $products = $this->getProducts($transaction);

        $html = PdfService::fromMustacheTemplate(
            $template->text_template,
            [
                'transaction' => [
                    'id' => $transaction->id,
                    'discount' => $transaction->total_discount,
                    'total' => $transaction->total_sold_price,
                    'shipping' => 0,
                    'tax' => 0,
                    'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                ],
                'products' => $products,
            ],
            true
        );

        $template = $this->parseReceiptTemplate($html);

        $printJob = new PrintJob();
        $printJob->printer_id = $request->printer_id;
        $printJob->title = "transaction $transaction->id receipt";
        $printJob->content = base64_encode($template);
        $printJob->content_type = 'raw_base64';
        $printJob->save();
        return true;

//        return $html;

//        $pdfString = PdfService::fromMustacheTemplate($template->html_template, [
//        $pdfString = PdfService::fromView('pdf/transaction/receipt', [
//            'transaction' => [
//                'id' => $transaction->id,
//                'subtotal' => $transaction->total_sold_price,
//                'total' => $transaction->total_sold_price,
//                'shipping' => 0,
//                'tax' => 0,
//                'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
//            ],
//            'shipping_address' => $transaction->shippingAddress->toArray(),
//            'billing_address' => $transaction->billingAddress->toArray(),
//            'products' => $products,
//        ]);

//        return response()->stream(function () use ($pdfString) {
//            echo $pdfString;
//        }, '200', ['Content-Type' => 'application/pdf']);

//        Mail::to($transaction->shippingAddress->email)->send($email);
//
//        ray($products);
//        return true;
    }

    private function getProducts(DataCollection $transaction): array
    {
        return $transaction->records->map(function ($record) {
            $product = $record->product;
            return [
                'sku' => $product->sku,
                'name' => $product->name,
                'quantity' => $record->quantity_scanned,
                'price' => $record->unit_sold_price,
            ];
        })->toArray();
    }

    private function parseReceiptTemplate(string $template): string
    {
        $codes = [
            'left' => 'ESC "a" 0',
            'center' => 'ESC "a" 1',
            'right' => 'ESC "a" 2',
            'font-large' => 'GS "!" 68',
            'font-big' => 'GS "!" 34',
            'font-normal' => 'GS "!" 0',
            'br' => 'ESC "d" 1',
            'cut' => 'GS "V" 1',
            'bold' => 'ESC "E" 1',
            'bold-off' => 'ESC "E" 0',
//            'barcode' => 'GS "H" 2',
            'tab' => 'HT',
            'dashed-line' => '------------------------------------------',
        ];

        if (str_contains($template, '<esc-left>')) {
            $template = str_replace('<esc-left>', $codes['left'], $template);
        }

        if (str_contains($template, '</esc-left>')) {
            $template = str_replace('</esc-left>', '', $template);
        }

        if (str_contains($template, '<esc-center>')) {
            $template = str_replace('<esc-center>', $codes['center'], $template);
        }

        if (str_contains($template, '</esc-center>')) {
            $template = str_replace('</esc-center>', $codes['left'], $template);
        }

        if (str_contains($template, '<esc-right>')) {
            $template = str_replace('<esc-right>', $codes['right'], $template);
        }

        if (str_contains($template, '</esc-right>')) {
            $template = str_replace('</esc-right>', $codes['left'], $template);
        }

        if (str_contains($template, '<esc-bold>')) {
            $template = str_replace('<esc-bold>', $codes['bold'], $template);
        }

        if (str_contains($template, '</esc-bold>')) {
            $template = str_replace('</esc-bold>', $codes['bold-off'], $template);
        }

        if (str_contains($template, '<esc-br>')) {
            $template = str_replace('<esc-br>', $codes['br'], $template);
        }

        if (str_contains($template, '</esc-br>')) {
            $template = str_replace('</esc-br>', '', $template);
        }

        if (str_contains($template, '<esc-cut>')) {
            $template = str_replace('<esc-cut>', $codes['cut'], $template);
        }

        if (str_contains($template, '</esc-cut>')) {
            $template = str_replace('</esc-cut>', '', $template);
        }

        if (str_contains($template, '<esc-font-large>')) {
            $template = str_replace('<esc-font-large>', $codes['font-large'], $template);
        }

        if (str_contains($template, '</esc-font-large>')) {
            $template = str_replace('</esc-font-large>', $codes['font-normal'], $template);
        }

        if (str_contains($template, '<esc-font-big>')) {
            $template = str_replace('<esc-font-big>', $codes['font-big'], $template);
        }

        if (str_contains($template, '</esc-font-big>')) {
            $template = str_replace('</esc-font-big>', $codes['font-normal'], $template);
        }

        if (str_contains($template, '<esc-font-normal>')) {
            $template = str_replace('<esc-font-normal>', $codes['font-normal'], $template);
        }

        if (str_contains($template, '</esc-font-normal>')) {
            $template = str_replace('</esc-font-normal>', '', $template);
        }

//        if (str_contains($template, '<esc-barcode>')) {
//            $template = str_replace('<esc-barcode>', $codes['barcode'], $template);
//        }

//        if (str_contains($template, '</esc-barcode>')) {
//            $template = str_replace('</esc-barcode>', $codes['font-normal'], $template);
//        }

        if (str_contains($template, '<esc-tab>')) {
            $template = str_replace('<esc-tab>', $codes['tab'], $template);
        }

        if (str_contains($template, '</esc-tab>')) {
            $template = str_replace('</esc-tab>', '', $template);
        }

        if (str_contains($template, '<esc-dashed-line>')) {
            $template = str_replace('<esc-dashed-line>', $codes['dashed-line'], $template);
        }

        if (str_contains($template, '</esc-dashed-line>')) {
            $template = str_replace('</esc-dashed-line>', '', $template);
        }

        ray($template);
        return $template;
    }
}
