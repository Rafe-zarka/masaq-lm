<?php

namespace App\Http\Controllers;

use App\Services\AiContentService;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function __construct(
        private readonly AiContentService $ai
    ) {}

    public function index()
    {
        return view('input');
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|min:20|max:10000',
            'mode'    => 'required|in:slides,video',
            'tone'    => 'required|in:professional,casual,academic,storytelling',
        ]);

        try {
            $data = $this->ai->generate(
                $validated['content'],
                $validated['mode'],
                $validated['tone']
            );

            session(['result' => ['mode' => $validated['mode'], 'data' => $data]]);

            return redirect()->route('result');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Something went wrong: ' . $e->getMessage());
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
