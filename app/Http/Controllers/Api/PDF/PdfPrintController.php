<?php

namespace App\Http\Controllers\Api\PDF;

use App\Http\Controllers\Controller;
use App\Modules\PrintNode\src\Models\PrintJob;
use App\Modules\PrintNode\src\PrintNode;
use App\Modules\PrintNode\src\Resources\PrintJobResource;
use App\Services\PdfService;
use Exception;
use Illuminate\Http\Request;

/**
 * Class PrintOrderController.
 */
class PdfPrintController extends Controller
{
    /**
     * @throws Exception
     */
    public function update(Request $request): PrintJobResource
    {
        $pdfString = PdfService::fromView('pdf/'.$request->template, $request->data);
        $printJob = new PrintJob();
        $printJob->printer_id = $request->user()->printer_id;
        $printJob->title = $request->template.'_by_'.$request->user()->id;
        $printJob->pdf = base64_encode($pdfString);

        PrintNode::print($printJob);

        return PrintJobResource::make($printJob);
    }
}
