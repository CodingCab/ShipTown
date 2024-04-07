<?php

namespace App\Abstracts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

abstract class ReportController extends Controller
{
    public function __invoke(Request $request): mixed
    {
        return $this->response($request);
    }

    abstract public function response(Request $request): mixed;
}
