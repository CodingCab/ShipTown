<?php

namespace App\Http\Controllers\Api\PDF;

use App\Http\Controllers\Controller;
use App\Services\PdfService;
use Exception;
use Illuminate\Http\Request;

/**
 * Class PrintOrderController.
 */
class PdfSaveController extends Controller
{
    /**
     * @throws Exception
     */
    public function update(Request $request)
    {
        $pdfString = PdfService::fromView('pdf/'.$request->template, $request->data);
        $timestamp = now()->timestamp;
        $filename = $timestamp.'_'.$request->template.'_by_'.$request->user()->id;
        \Storage::disk('public')->put('pdf/'.$filename.'.pdf', $pdfString);
        $downloadUrl = \Storage::url('pdf/'.$filename.'.pdf');

        return response()->json(['filename' => $filename, 'download_url' => $downloadUrl]);
    }
}
