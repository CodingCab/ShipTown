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
        $template = $request->template;
        $pdfOutput = PdfService::fromView('pdf/'.$template, $request->data);

        return response()->make($pdfOutput, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="filename.pdf"'
        ]);
    }
}
