<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PdfService;
use Exception;
use Illuminate\Http\Request;

/**
 * Class PrintOrderController.
 */
class PreviewShelfLabelController extends Controller
{
    /**
     * @throws Exception
     */
    public function update(Request $request)
    {
        $view = 'pdf/shelf-labels/'.$request->templateType;

        return PdfService::fromView($view, ['labels' => $request->labels]);
    }
}
