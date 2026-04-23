<?php

namespace App\Http\Controllers;

use App\Services\AiContentService;
use App\Services\PdfParserService;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function __construct(
        private readonly AiContentService $ai,
        private readonly PdfParserService $pdf,
    ) {}

    public function index()
    {
        return view('input');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'mode' => 'required|in:slides,video',
            'tone' => 'required|in:professional,casual,academic,storytelling',
        ]);

        // At least one input must be provided
        $hasPdf  = $request->hasFile('pdf') && $request->file('pdf')->isValid();
        $hasText = filled($request->input('content'));

        if (! $hasPdf && ! $hasText) {
            return back()
                ->withInput()
                ->withErrors(['content' => 'Please paste some text or upload a PDF.']);
        }

        // Validate the chosen input type
        if ($hasPdf) {
            $request->validate([
                'pdf' => 'file|mimes:pdf|max:5120',
            ]);
        } else {
            $request->validate([
                'content' => 'required|string|min:20|max:10000',
            ]);
        }

        try {
            if ($hasPdf) {
                $content    = $this->pdf->extract($request->file('pdf')->getRealPath());
                $sourceType = 'pdf';
                $sourceName = $request->file('pdf')->getClientOriginalName();
            } else {
                $content    = trim($request->input('content'));
                $sourceType = 'text';
                $sourceName = null;
            }

            $data = $this->ai->generate($content, $request->input('mode'), $request->input('tone'));

            session([
                'result' => [
                    'mode'       => $request->input('mode'),
                    'data'       => $data,
                    'sourceType' => $sourceType,
                    'sourceName' => $sourceName,
                ],
            ]);

            return redirect()->route('result');

        } catch (\Throwable $e) {
            // If AI failed but we have content, fall back to mock
            if (str_contains($e->getMessage(), 'Anthropic API') || str_contains($e->getMessage(), 'parse')) {
                try {
                    $data = $this->ai->fallbackMock($request->input('mode'));
                    session([
                        'result' => [
                            'mode'       => $request->input('mode'),
                            'data'       => $data,
                            'sourceType' => $sourceType ?? 'text',
                            'sourceName' => $sourceName ?? null,
                        ],
                    ]);
                    return redirect()->route('result')->with('warning', 'AI generation failed — showing sample output instead.');
                } catch (\Throwable) {}
            }

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function result()
    {
        $result = session('result');

        if (! $result) {
            return redirect()->route('home');
        }

        return view('result', compact('result'));
    }
}
