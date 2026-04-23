<?php

namespace App\Http\Controllers;

use App\Services\ImageGenerationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function __construct(
        private readonly ImageGenerationService $images,
    ) {}

    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
        ]);

        try {
            $url = $this->images->generate($request->input('prompt'));
            return response()->json(['url' => $url]);
        } catch (\Throwable $e) {
            logger()->warning('masaq-lm image generation failed', ['message' => $e->getMessage()]);
            return response()->json(['url' => '', 'error' => true]);
        }
    }
}
