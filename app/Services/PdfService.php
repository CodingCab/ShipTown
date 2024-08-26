<?php

namespace App\Services;

use Dompdf\Dompdf;
use Illuminate\Contracts\Container\BindingResolutionException;

class PdfService
{
    /**
     * @throws BindingResolutionException
     */
    public static function fromView(string $view, array $data) : Dompdf | string
    {
        $html = view()->make($view, $data);

        return self::fromHtml($html, $data);
    }

    public static function fromHtml(string $template, array $data) : string
    {
        $dompdf = new Dompdf();
        $dompdf->loadHtml($template);
        $dompdf->render();

        return $dompdf->output();
    }
}
