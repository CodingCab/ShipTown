<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentIndexRequest;
use App\Models\DataCollection;
use App\Models\MailTemplate;
use App\Services\PdfService;
use Mustache_Engine;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function index(DocumentIndexRequest $request, Mustache_Engine $engine): StreamedResponse|string
    {
        /** @var MailTemplate $template */
        $template = MailTemplate::query()
            ->where('code', $request->validated('template_code'))
            ->first();

        $pdfString = PdfService::fromMustacheTemplate($template->html_template, [
            'data_collection' => DataCollection::query()->where('id', $request->validated('data_collection_id'))->first(),
        ]);

        switch ($request->validated('output_format', 'pdf')) {
            case 'pdf':
                return response()->stream(function () use ($pdfString) {
                    echo $pdfString;
                }, '200', ['Content-Type' => 'application/pdf']);
            default:
                return $pdfString;
        }
    }
}
