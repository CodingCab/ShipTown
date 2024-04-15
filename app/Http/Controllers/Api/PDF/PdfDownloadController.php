<?php

namespace App\Http\Controllers\Api\PDF;

use App\Http\Controllers\Controller;
use App\Services\PdfService;
use Exception;
use Illuminate\Http\Request;

/**
 * Class PrintOrderController.
 */
class PdfDownloadController extends Controller
{
    /**
     * @throws Exception
     */
    public function update(Request $request)
    {
        $pdfOutput = PdfService::fromView('pdf/'.$request->template, $request->data, true);
        $templateName = str_replace('/', '_', $request->template);

        return response()->streamDownload(function () use ($pdfOutput, $templateName) {
            echo $pdfOutput->output();
        }, $templateName.'.pdf', ['Content-Type' => 'application/pdf']);
    }
}
