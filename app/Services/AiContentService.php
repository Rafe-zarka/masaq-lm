<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AiContentService
{
    private string $apiKey;
    private string $model = 'claude-haiku-4-5-20251001';
    private string $apiUrl = 'https://api.anthropic.com/v1/messages';

    public function __construct()
    {
        // config() may return empty if config:cache ran before env vars were injected
        // env() reads the live system environment and works correctly on Laravel Cloud
        $this->apiKey = config('services.anthropic.key') ?: env('ANTHROPIC_API_KEY', '');
    }

    public function fallbackMock(string $mode): array
    {
        return $this->mock($mode);
    }

    public function generate(string $content, string $mode, string $tone): array
    {
        if (empty($this->apiKey)) {
            return $this->mock($mode);
        }

        // Guard against excessively long content
        $content = $this->trimContent($content);

        $prompt = $mode === 'slides'
            ? $this->slidesPrompt($content, $tone)
            : $this->videoPrompt($content, $tone);

        $response = Http::withHeaders([
            'x-api-key'         => $this->apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type'      => 'application/json',
        ])->withOptions(['verify' => ! app()->isLocal()])->timeout(60)->post($this->apiUrl, [
            'model'      => $this->model,
            'max_tokens' => 1024,
            'messages'   => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException('Anthropic API error: ' . $response->body());
        }

        $raw = $response->json('content.0.text', '');
        $clean = preg_replace('/```json\s*/i', '', $raw);
        $clean = preg_replace('/```\s*/', '', $clean);
        $clean = trim($clean);

        $decoded = json_decode($clean, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Failed to parse AI response as JSON.');
        }

        return $decoded;
    }

    private function trimContent(string $content, int $maxChars = 6000): string
    {
        if (mb_strlen($content) <= $maxChars) {
            return $content;
        }
        $trimmed   = mb_substr($content, 0, $maxChars);
        $lastSpace = mb_strrpos($trimmed, ' ');
        return $lastSpace ? mb_substr($trimmed, 0, $lastSpace) : $trimmed;
    }

    private function slidesPrompt(string $content, string $tone): string
    {
        return <<<PROMPT
You are a presentation designer. Transform the following content into a structured slide deck.

Tone: {$tone}

Content:
{$content}

Return ONLY a valid JSON object with this exact structure:
{
  "title": "Presentation title",
  "subtitle": "Brief subtitle or tagline",
  "slides": [
    {
      "title": "Slide title",
      "bullets": ["Point one", "Point two", "Point three"],
      "visual": "Brief hint for a visual/image (optional, 1 short sentence)"
    }
  ]
}

Rules:
- Create 4-7 slides
- Each slide has exactly 3 bullet points
- Bullets are concise, actionable, punchy (max 15 words each)
- Titles are short and clear
- Visual hints are optional but helpful
- Return ONLY the JSON, no markdown, no explanation
PROMPT;
    }

    private function videoPrompt(string $content, string $tone): string
    {
        return <<<PROMPT
You are a video script writer. Transform the following content into a compelling video script.

Tone: {$tone}

Content:
{$content}

Return ONLY a valid JSON object with this exact structure:
{
  "title": "Video title",
  "hook": "Opening hook sentence (the very first thing said)",
  "duration": "estimated duration e.g. '3-4 min'",
  "sections": [
    {
      "label": "Intro",
      "duration": "~30s",
      "lines": [
        "Line of spoken script...",
        "[Stage direction or visual note in brackets]",
        "More spoken words..."
      ]
    }
  ]
}

Rules:
- Sections: Intro, Main Content (2-3 parts), Outro, CTA
- Lines are natural spoken language, conversational
- Include [stage directions] in brackets for visual cues
- Tone matches the requested style
- Return ONLY the JSON, no markdown, no explanation
PROMPT;
    }

    private function mock(string $mode): array
    {
        if ($mode === 'slides') {
            return [
                'title'    => 'AI-Powered Productivity in 2025',
                'subtitle' => 'How modern tools are reshaping knowledge work',
                'slides'   => [
                    [
                        'title'   => 'The New Productivity Landscape',
                        'bullets' => [
                            'AI tools have cut repetitive tasks by up to 40%',
                            'Knowledge workers focus more on strategy than execution',
                            'Automation is no longer optional — it\'s competitive',
                        ],
                        'visual' => 'Split-screen: cluttered desk vs. clean modern workspace',
                    ],
                    [
                        'title'   => 'Key Drivers of Adoption',
                        'bullets' => [
                            'Better language models with deeper context understanding',
                            'API costs dropped 10× in two years — startups can afford it',
                            'No-code interfaces removed the engineering barrier',
                        ],
                        'visual' => 'Upward trend chart with AI adoption curve',
                    ],
                    [
                        'title'   => 'The Trust Problem',
                        'bullets' => [
                            'Capability is solved — workflow trust is the new frontier',
                            'Teams resist tools that disrupt established processes',
                            'Transparency and explainability build long-term confidence',
                        ],
                        'visual' => 'Person reviewing AI output before approving it',
                    ],
                    [
                        'title'   => 'What Great Looks Like',
                        'bullets' => [
                            'Tools that feel invisible and native to existing workflows',
                            'Measurable ROI visible within the first week',
                            'Human remains in the loop for all key decisions',
                        ],
                        'visual' => 'Minimalist UI showing a clean AI assistant integrated in a dashboard',
                    ],
                    [
                        'title'   => 'Next Steps',
                        'bullets' => [
                            'Pilot one AI tool in your highest-friction workflow',
                            'Measure time saved vs. baseline over 30 days',
                            'Share learnings and scale what works',
                        ],
                        'visual' => 'Checklist with three bold items ticked off',
                    ],
                ],
            ];
        }

        return [
            'title'    => 'The Future of Work Is Here — And It\'s AI',
            'hook'     => 'What if the most valuable thing you could do today is let AI handle tomorrow\'s workload?',
            'duration' => '3-4 min',
            'sections' => [
                [
                    'label'    => 'Intro',
                    'duration' => '~30s',
                    'lines'    => [
                        'Hey — welcome back.',
                        '[Cut to presenter at clean desk, good lighting]',
                        'Today we\'re talking about something that\'s quietly changing how the best teams operate.',
                        'AI productivity tools. Not the hype. The real, daily-use stuff.',
                    ],
                ],
                [
                    'label'    => 'Main Content 1',
                    'duration' => '~60s',
                    'lines'    => [
                        'Here\'s what the data actually shows.',
                        'Companies using AI in their core workflows are reporting 30 to 40 percent productivity gains.',
                        '[Show graph on screen]',
                        'But — and this is important — that number only holds when the tool fits the workflow.',
                        'Most failed AI rollouts happen because someone bought a tool and expected magic.',
                    ],
                ],
                [
                    'label'    => 'Main Content 2',
                    'duration' => '~60s',
                    'lines'    => [
                        'The real unlock? Trust.',
                        '[Zoom in to presenter]',
                        'Your team won\'t use a tool they don\'t trust. It\'s that simple.',
                        'So the question isn\'t "which AI is best?" — it\'s "which one will my team actually open tomorrow morning?"',
                        'Start small. One workflow. Measure the time saved. Then scale what works.',
                    ],
                ],
                [
                    'label'    => 'Outro',
                    'duration' => '~30s',
                    'lines'    => [
                        'The teams winning right now aren\'t the ones with the biggest budgets.',
                        'They\'re the ones moving deliberately — testing, learning, and compounding gains.',
                        '[Soft background music fades in]',
                        'You can be one of them.',
                    ],
                ],
                [
                    'label'    => 'CTA',
                    'duration' => '~20s',
                    'lines'    => [
                        'If this was useful, hit subscribe — we break down AI tools for real work every week.',
                        '[Smile, point to subscribe button on screen]',
                        'And drop a comment: what\'s the one task you wish AI could take off your plate?',
                        'See you in the next one.',
                    ],
                ],
            ],
        ];
    }
}
