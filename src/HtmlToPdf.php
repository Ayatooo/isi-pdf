<?php

namespace IsiPdf\PdfGenerator;

use Exception;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

class HtmlToPdf
{
    protected mixed $html;
    protected string $outputPath;
    protected string $orientation;

    public function __construct($html, $outputPath = null)
    {
        $this->html = $html;
        $this->html = mb_convert_encoding($this->html, 'HTML-ENTITIES', 'UTF-8');
        $this->outputPath = $outputPath ?? config('pdf-generator.output_path');
        $this->orientation = config('pdf-generator.default_orientation') ?? 'portrait';
    }

    /**
     * @param string $orientation
     * @return $this
     */
    public function setOrientation(string $orientation): static
    {
        if (in_array($orientation, ['portrait', 'landscape'])) {
            $this->orientation = $orientation;
        } else {
            throw new InvalidArgumentException("Invalid orientation: $orientation. Allowed: 'portrait', 'landscape'.");
        }
        return $this;
    }

    /**
     * @throws Exception
     * @throws RuntimeException
     * @param string $fileName
     * @return string
     */
    public function render(string $fileName): string
    {
        $htmlFile = tempnam(sys_get_temp_dir(), 'html') . '.html';
        $pdfFile = $this->outputPath . '/' . $fileName . '.pdf';

        file_put_contents($htmlFile, $this->html);

        // Ajouter l'option --encoding utf-8
        $command = escapeshellcmd("wkhtmltopdf --orientation $this->orientation --encoding utf-8 $htmlFile $pdfFile");
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new RuntimeException("wkhtmltopdf failed: " . implode("\n", $output));
        }

        unlink($htmlFile);

        return $pdfFile;
    }

    /**
     * @param mixed $view
     * @param array $data
     * @param string $fileName
     * @param string $orientation
     * @return string
     * @throws Throwable
     */
    public static function generateFromView(mixed $view, array $data, string $fileName, string $orientation = 'portrait'): string
    {
        $html = view($view, $data)->render();
        $instance = new self($html);
        $instance->setOrientation($orientation);

        return $instance->render($fileName);
    }
}

