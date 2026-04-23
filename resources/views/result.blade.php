@extends('layouts.app')

@section('title', $result['mode'] === 'slides' ? 'Your Slides — masaq LM' : 'Video Script — masaq LM')

@section('content')
<div class="page-enter" style="min-height:100vh;display:flex;flex-direction:column;">

  {{-- Sticky header --}}
  <header style="padding:18px 32px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border);background:var(--surface);position:sticky;top:0;z-index:10;">
    <div style="display:flex;align-items:center;gap:16px;">
      <a href="{{ route('home') }}"
        style="display:flex;align-items:center;gap:6px;background:none;border:1px solid var(--border);border-radius:8px;padding:7px 12px;cursor:pointer;font-family:var(--font-body);font-size:13px;color:var(--text-2);text-decoration:none;transition:all .15s;"
        onmouseenter="this.style.borderColor='var(--accent)'"
        onmouseleave="this.style.borderColor='var(--border)'">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
        Back
      </a>
      <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
        @if($result['mode'] === 'slides')
          <span style="font-family:var(--font-head);font-weight:700;font-size:15px;">Your Slides</span>
          <span style="font-size:12px;color:var(--text-3);background:var(--surface2);padding:2px 8px;border-radius:12px;border:1px solid var(--border);">
            {{ count($result['data']['slides'] ?? []) }} slides
          </span>
        @else
          <span style="font-family:var(--font-head);font-weight:700;font-size:15px;">Video Script</span>
          @if(!empty($result['data']['duration']))
            <span style="font-size:12px;color:var(--text-3);background:var(--surface2);padding:2px 8px;border-radius:12px;border:1px solid var(--border);">
              ~{{ $result['data']['duration'] }}
            </span>
          @endif
        @endif
        {{-- Source badge --}}
        @if(($result['sourceType'] ?? 'text') === 'pdf')
          <span style="font-size:11px;color:var(--accent);background:var(--accent-soft);padding:2px 8px;border-radius:12px;border:1px solid var(--accent-mid);display:inline-flex;align-items:center;gap:4px;">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            PDF
          </span>
        @else
          <span style="font-size:11px;color:var(--text-3);background:var(--surface2);padding:2px 8px;border-radius:12px;border:1px solid var(--border);">
            Text
          </span>
        @endif
      </div>
    </div>

    {{-- Copy button --}}
    <button id="copy-btn"
      onclick="copyContent()"
      style="display:flex;align-items:center;gap:6px;background:var(--surface);border:1px solid var(--border);border-radius:8px;padding:7px 14px;cursor:pointer;font-family:var(--font-body);font-size:13px;color:var(--text-2);font-weight:500;transition:all .2s;">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/>
      </svg>
      <span id="copy-label">{{ $result['mode'] === 'slides' ? 'Copy all' : 'Copy script' }}</span>
    </button>
  </header>

  {{-- Main content --}}
  <main style="flex:1;padding:40px 32px;max-width:{{ $result['mode'] === 'slides' ? '900px' : '760px' }};margin:0 auto;width:100%;">

    @if(session('warning'))
    <div style="margin-bottom:20px;padding:12px 16px;background:#FFFBEB;border:1px solid #FDE68A;border-radius:var(--radius-sm);color:#92400E;font-size:13px;">
      {{ session('warning') }}
    </div>
    @endif

    @if($result['mode'] === 'slides')
      {{-- ── SLIDES ── --}}
      @if(!empty($result['data']['title']))
        <div style="margin-bottom:32px;">
          <h2 style="font-family:var(--font-head);font-size:22px;font-weight:700;color:var(--text);margin-bottom:6px;">
            {{ $result['data']['title'] }}
          </h2>
          @if(!empty($result['data']['subtitle']))
            <p style="color:var(--text-2);font-size:14px;">{{ $result['data']['subtitle'] }}</p>
          @endif
        </div>
      @endif

      <div style="display:flex;flex-direction:column;gap:16px;">
        @foreach($result['data']['slides'] as $index => $slide)
          @php $total = count($result['data']['slides']); @endphp
          <div class="slide-card" style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow-sm);animation-delay:{{ $index * 0.07 }}s;transition:box-shadow .2s,transform .2s;"
            onmouseenter="this.style.boxShadow='var(--shadow-md)';this.style.transform='translateY(-2px)';"
            onmouseleave="this.style.boxShadow='var(--shadow-sm)';this.style.transform='none';">
            <div style="display:grid;grid-template-columns:1fr {{ !empty($slide['visual']) ? 'auto' : '' }};">
              <div style="padding:24px 28px;">
                {{-- Slide number --}}
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                  <span style="width:26px;height:26px;border-radius:7px;background:var(--accent-soft);border:1px solid var(--accent-mid);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:var(--accent);font-family:var(--font-head);">
                    {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                  </span>
                  <span style="font-size:11px;color:var(--text-3);font-weight:500;">of {{ $total }}</span>
                </div>
                {{-- Title --}}
                <h3 style="font-family:var(--font-head);font-size:18px;font-weight:700;color:var(--text);margin-bottom:14px;line-height:1.3;letter-spacing:-0.01em;">
                  {{ $slide['title'] }}
                </h3>
                {{-- Bullets --}}
                <ul style="list-style:none;display:flex;flex-direction:column;gap:9px;">
                  @foreach($slide['bullets'] as $bullet)
                    <li style="display:flex;gap:10px;align-items:flex-start;">
                      <span style="width:5px;height:5px;border-radius:50%;background:var(--accent);flex-shrink:0;margin-top:8px;"></span>
                      <span style="font-size:14px;color:var(--text-2);line-height:1.6;">{{ $bullet }}</span>
                    </li>
                  @endforeach
                </ul>
              </div>
              {{-- Visual hint --}}
              @if(!empty($slide['visual']))
                <div style="width:160px;background:var(--surface2);border-left:1px solid var(--border);display:flex;flex-direction:column;align-items:center;justify-content:center;padding:20px 16px;gap:8px;">
                  <div style="width:42px;height:42px;border-radius:10px;background:var(--border);display:flex;align-items:center;justify-content:center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--text-3)" stroke-width="1.5">
                      <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/>
                    </svg>
                  </div>
                  <p style="font-size:10px;color:var(--text-3);text-align:center;line-height:1.4;font-style:italic;">{{ $slide['visual'] }}</p>
                </div>
              @endif
            </div>
          </div>
        @endforeach
      </div>

    @else
      {{-- ── VIDEO SCRIPT ── --}}
      <div style="margin-bottom:36px;padding-bottom:28px;border-bottom:1px solid var(--border);">
        <h2 style="font-family:var(--font-head);font-size:24px;font-weight:800;color:var(--text);letter-spacing:-0.02em;margin-bottom:8px;">
          {{ $result['data']['title'] }}
        </h2>
        @if(!empty($result['data']['hook']))
          <p style="font-size:15px;color:var(--text-2);line-height:1.7;font-style:italic;">"{{ $result['data']['hook'] }}"</p>
        @endif
      </div>

      <div style="display:flex;flex-direction:column;gap:28px;">
        @foreach($result['data']['sections'] as $index => $section)
          @php
            $labelColors = [
              'INTRO'  => ['bg' => 'oklch(0.94 0.06 280)', 'text' => 'oklch(0.45 0.15 280)', 'border' => 'oklch(0.88 0.08 280)'],
              'HOOK'   => ['bg' => 'oklch(0.95 0.06 60)',  'text' => 'oklch(0.45 0.15 60)',  'border' => 'oklch(0.88 0.08 60)'],
              'MAIN'   => ['bg' => 'var(--accent-soft)',    'text' => 'var(--accent)',         'border' => 'var(--accent-mid)'],
              'OUTRO'  => ['bg' => 'oklch(0.94 0.06 140)', 'text' => 'oklch(0.40 0.12 140)', 'border' => 'oklch(0.88 0.08 140)'],
              'CTA'    => ['bg' => 'oklch(0.95 0.05 30)',  'text' => 'oklch(0.48 0.14 30)',  'border' => 'oklch(0.88 0.08 30)'],
            ];
            $key = strtoupper(preg_replace('/\s+\d+/', '', $section['label']));
            $key = explode(' ', $key)[0];
            $colors = $labelColors[$key] ?? ['bg' => 'var(--surface2)', 'text' => 'var(--text-2)', 'border' => 'var(--border)'];
          @endphp
          <div class="slide-card" style="display:flex;gap:0;animation-delay:{{ $index * 0.08 }}s;">
            <div style="width:3px;border-radius:3px 0 0 3px;background:{{ $colors['text'] }};flex-shrink:0;"></div>
            <div style="flex:1;background:var(--surface);border:1px solid var(--border);border-left:none;border-radius:0 var(--radius) var(--radius) 0;padding:20px 24px;">
              <div style="margin-bottom:12px;">
                <span style="display:inline-block;background:{{ $colors['bg'] }};color:{{ $colors['text'] }};border:1px solid {{ $colors['border'] }};border-radius:6px;padding:3px 9px;font-size:10px;font-weight:700;font-family:var(--font-head);letter-spacing:.06em;">
                  {{ strtoupper($section['label']) }}
                </span>
                @if(!empty($section['duration']))
                  <span style="margin-left:8px;font-size:11px;color:var(--text-3);">{{ $section['duration'] }}</span>
                @endif
              </div>
              <div style="display:flex;flex-direction:column;gap:10px;">
                @foreach($section['lines'] as $line)
                  <p style="font-size:15px;line-height:1.75;color:{{ str_starts_with($line, '[') ? 'var(--text-3)' : 'var(--text)' }};font-style:{{ str_starts_with($line, '[') ? 'italic' : 'normal' }};">
                    {{ $line }}
                  </p>
                @endforeach
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endif

  </main>
</div>
@endsection

@push('scripts')
<script>
const copyData = @json($result);

function copyContent() {
  let text = '';
  if (copyData.mode === 'slides') {
    text = copyData.data.slides.map((s, i) =>
      `Slide ${i+1}: ${s.title}\n${s.bullets.map(b => `• ${b}`).join('\n')}${s.visual ? `\n[Visual: ${s.visual}]` : ''}`
    ).join('\n\n');
  } else {
    text = `${copyData.data.title}\n\n${copyData.data.sections.map(s =>
      `[${s.label}]\n${s.lines.join('\n')}`
    ).join('\n\n')}`;
  }

  navigator.clipboard.writeText(text).then(() => {
    const btn = document.getElementById('copy-btn');
    const label = document.getElementById('copy-label');
    const origLabel = label.textContent;
    btn.style.background = 'var(--accent-soft)';
    btn.style.borderColor = 'var(--accent-mid)';
    btn.style.color = 'var(--accent)';
    label.textContent = 'Copied!';
    setTimeout(() => {
      btn.style.background = 'var(--surface)';
      btn.style.borderColor = 'var(--border)';
      btn.style.color = 'var(--text-2)';
      label.textContent = origLabel;
    }, 2000);
  });
}
</script>
@endpush
