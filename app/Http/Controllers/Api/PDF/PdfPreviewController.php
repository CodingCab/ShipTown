<?php

namespace App\Http\Controllers\Api\PDF;

use App\Http\Controllers\Controller;
use App\Services\PdfService;
use Exception;
use Illuminate\Http\Request;

class PdfPreviewController extends Controller
{
    /**
     * @throws Exception
     */
    public function update(Request $request)
    {
        return PdfService::fromView('pdf/'.$request->template, $request->data);
    }
}
