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

//    public function printReceipt(PrintReceiptRequest $request): StreamedResponse
    public function printReceipt(): StreamedResponse
    {
//        $transaction = DataCollection::findOrFail($request->validated('id'));
        $transaction = DataCollection::findOrFail(13);

        /** @var MailTemplate $template */
        $template = MailTemplate::query()
            ->where('code', 'transaction_receipt')
            ->where('mailable', TransactionReceiptMail::class)
            ->first();

        $products = $this->getProducts($transaction);

//        PdfService::fromMustacheTemplate($template->html_template, [
//            'order' => $order->toArray(),
//            'shipments' => $order->orderShipments->toArray(),
//            'shipping_address' => $order->shippingAddress->toArray(),
//        ]);

        $pdfString = PdfService::fromMustacheTemplate($template->html_template, [
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

        return response()->stream(function () use ($pdfString) {
            echo $pdfString;
        }, '200', ['Content-Type' => 'application/pdf']);

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
                'name' => $product->name,
                'quantity' => $record->quantity_scanned,
                'price' => $record->unit_sold_price,
            ];
        })->toArray();
    }
}
