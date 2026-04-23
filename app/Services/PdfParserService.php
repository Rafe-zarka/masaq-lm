<?php

namespace App\Services;

use Smalot\PdfParser\Parser;

class PdfParserService
{
    private const MAX_CHARS = 8000;

    public function extract(string $filePath): string
    {
        $parser = new Parser();
        $pdf    = $parser->parseFile($filePath);
        $text   = $pdf->getText();

        $text = $this->clean($text);

        if (empty(trim($text))) {
            throw new \RuntimeException('The PDF appears to be empty or image-only. Please use a text-based PDF.');
        }

        // Trim to avoid token overflow while keeping coherent sentences
        if (mb_strlen($text) > self::MAX_CHARS) {
            $text = mb_substr($text, 0, self::MAX_CHARS);
            // Cut at last whitespace so we don't split mid-word
            $lastSpace = mb_strrpos($text, ' ');
            if ($lastSpace !== false) {
                $text = mb_substr($text, 0, $lastSpace);
            }
            $text .= "\n\n[Content trimmed — PDF was longer than the processing limit.]";
        }

        return trim($text);
    }

    private function clean(string $text): string
    {
        // Normalize line endings
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        // Collapse runs of blank lines to a single blank line
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        // Remove non-printable characters except newlines and tabs
        $text = preg_replace('/[^\P{C}\n\t]/u', '', $text);

        return $text;
    }
}
