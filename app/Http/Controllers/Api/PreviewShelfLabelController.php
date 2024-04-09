<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PdfService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        Log::info('PreviewShelfLabelController@update', ['request' => $request->all()]);


        $view = 'pdf/'.$request->template;

        Log::info('PreviewShelfLabelController@update', ['view' => $view]);

        return PdfService::fromView($view, ['labels' => $request->labels]);
    }
}
