<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ImageGenerationService
{
    private array $stopWords = [
        'a', 'an', 'the', 'of', 'in', 'for', 'on', 'with', 'at', 'to', 'and',
        'or', 'is', 'are', 'was', 'be', 'as', 'by', 'its', 'this', 'that',
        'from', 'have', 'been', 'will', 'can', 'each', 'very', 'more', 'their',
        'show', 'shows', 'showing', 'scene', 'image', 'photo', 'visual',
        'depicting', 'illustrating', 'represent', 'represents', 'across',
        'landscape', 'abstract', 'concept', 'professional', 'clean', 'modern',
        'background', 'style', 'design', 'minimal', 'sharp', 'focus', 'high',
        'quality', 'detailed', 'illustration', 'showing',
    ];

    public function isEnabled(): bool
    {
        return true;
    }

    public function generate(string $prompt): string
    {
        $keywords = $this->extractKeywords($prompt);
        $query    = implode(' ', $keywords) ?: $prompt;

        $response = Http::withOptions(['verify' => ! app()->isLocal()])
            ->timeout(10)
            ->get('https://api.openverse.org/v1/images/', [
                'q'            => $query,
                'page_size'    => 10,
                'license_type' => 'commercial',
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException('Openverse API error: ' . $response->status());
        }

        $results = $response->json('results', []);

        if (empty($results)) {
            return '';
        }

        $idx = abs(crc32($prompt)) % count($results);

        return $results[$idx]['url'] ?? $results[0]['url'] ?? '';
    }

    private function extractKeywords(string $prompt): array
    {
        $clean = preg_replace('/[^a-zA-Z\s]/', ' ', strtolower($prompt));
        $words = preg_split('/\s+/', trim($clean));

        return array_values(
            array_slice(
                array_filter($words, fn ($w) => strlen($w) > 4 && ! in_array($w, $this->stopWords)),
                0, 4
            )
        );
    }
}
