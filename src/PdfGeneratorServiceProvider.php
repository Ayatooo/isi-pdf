<?php

namespace IsiPdf\PdfGenerator;

use Illuminate\Support\ServiceProvider;

class PdfGeneratorServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/config/pdf-generator.php' => config_path('pdf-generator.php'),
        ], 'config');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/pdf-generator.php',
            'pdf-generator'
        );
    }
}

