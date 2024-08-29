<?php

namespace App\Services;

use Dompdf\Dompdf;
use Illuminate\Contracts\Container\BindingResolutionException;
use Mustache_Engine;

class PdfService
{
    public static function fromMustacheTemplate(string $template, array $data) : string
    {
        $engine = new Mustache_Engine();

        $html = $engine->render($template, $data);

        return self::fromHtml($html);
    }

    /**
     * @throws BindingResolutionException
     */
    public static function fromView(string $view, array $data) : Dompdf | string
    {
        $html = view()->make($view, $data);

        return self::fromHtml($html);
    }

    public static function fromHtml(string $html) : string
    {
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();

        return $dompdf->output();
    }
}
