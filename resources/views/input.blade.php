@extends('layouts.app')

@section('title', 'masaq LM — Turn content into slides or video')

@section('content')
<div class="page-enter" style="min-height:100vh;display:flex;flex-direction:column;">

  {{-- Header --}}
  <header style="padding:24px 32px;display:flex;align-items:center;justify-content:space-between;">
    <div style="display:flex;align-items:center;gap:8px;">
      <div style="width:28px;height:28px;border-radius:8px;background:var(--accent);display:flex;align-items:center;justify-content:center;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="white">
          <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
        </svg>
      </div>
      <span style="font-family:var(--font-head);font-weight:700;font-size:16px;letter-spacing:-0.01em;">masaq<span style="color:var(--accent);">LM</span></span>
    </div>
    <span style="font-size:12px;color:var(--text-3);background:var(--surface2);padding:4px 10px;border-radius:20px;border:1px solid var(--border);">
      AI-powered · Free
    </span>
  </header>

  {{-- Main --}}
  <main style="flex:1;display:flex;align-items:center;justify-content:center;padding:32px 24px 80px;">
    <div style="width:100%;max-width:680px;">

      {{-- Headline --}}
      <div style="margin-bottom:40px;text-align:center;">
        <h1 style="font-family:var(--font-head);font-size:clamp(28px,5vw,46px);font-weight:800;line-height:1.1;letter-spacing:-0.03em;color:var(--text);margin-bottom:14px;">
          Turn your content into<br>
          <span style="color:var(--accent);">slides or video</span>
        </h1>
        <p style="color:var(--text-2);font-size:16px;font-weight:300;line-height:1.6;">
          Paste your raw content below — notes, article, ideas, anything —<br>
          and we'll shape it into something presentable.
        </p>
      </div>

      {{-- Error alert --}}
      @if(session('error'))
      <div style="margin-bottom:20px;padding:12px 16px;background:#FEF2F2;border:1px solid #FECACA;border-radius:var(--radius-sm);color:#DC2626;font-size:13px;">
        {{ session('error') }}
      </div>
      @endif

      {{-- Card --}}
      <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow-md);overflow:hidden;">
        <form id="generate-form" method="POST" action="{{ route('generate') }}">
          @csrf

          {{-- Textarea --}}
          <div style="position:relative;">
            <textarea
              id="content-input"
              name="content"
              placeholder="Paste your raw content here…&#10;&#10;e.g. meeting notes, article draft, talking points, research summary…"
              style="width:100%;min-height:240px;padding:24px;border:none;outline:none;resize:vertical;font-family:var(--font-body);font-size:15px;line-height:1.7;color:var(--text);background:transparent;caret-color:var(--accent);"
            >{{ old('content') }}</textarea>
            <div id="char-count" style="position:absolute;bottom:10px;right:16px;font-size:11px;color:var(--text-3);font-variant-numeric:tabular-nums;">0 chars</div>
          </div>

          {{-- Toolbar --}}
          <div style="border-top:1px solid var(--border);padding:14px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;background:var(--surface2);">

            {{-- Tone selector --}}
            <div style="display:flex;align-items:center;gap:8px;">
              <label for="tone" style="font-size:12px;color:var(--text-3);font-weight:500;">Tone</label>
              <select name="tone" id="tone" style="font-family:var(--font-body);font-size:13px;font-weight:500;color:var(--text);background:var(--surface);border:1px solid var(--border);border-radius:8px;padding:6px 28px 6px 10px;cursor:pointer;outline:none;appearance:none;background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%236B6860' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 8px center;">
                <option value="professional" {{ old('tone') === 'professional' || !old('tone') ? 'selected' : '' }}>Professional</option>
                <option value="casual" {{ old('tone') === 'casual' ? 'selected' : '' }}>Casual</option>
                <option value="academic" {{ old('tone') === 'academic' ? 'selected' : '' }}>Academic</option>
                <option value="storytelling" {{ old('tone') === 'storytelling' ? 'selected' : '' }}>Storytelling</option>
              </select>
            </div>

            {{-- Action buttons --}}
            <div style="display:flex;gap:8px;">
              <button type="submit" name="mode" value="slides" id="btn-slides"
                style="display:flex;align-items:center;gap:7px;padding:9px 16px;border-radius:9px;font-family:var(--font-body);font-size:13px;font-weight:600;cursor:pointer;transition:all .18s;border:1px solid var(--border);background:var(--surface);color:var(--text-2);"
                onmouseenter="this.style.background='var(--surface2)';this.style.color='var(--text)';"
                onmouseleave="this.style.background='var(--surface)';this.style.color='var(--text-2)';">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>
                </svg>
                Generate Slides
              </button>
              <button type="submit" name="mode" value="video" id="btn-video"
                style="display:flex;align-items:center;gap:7px;padding:9px 16px;border-radius:9px;font-family:var(--font-body);font-size:13px;font-weight:600;cursor:pointer;transition:all .18s;border:none;background:var(--accent);color:white;box-shadow:0 2px 8px oklch(0.58 0.13 168 / .35);"
                onmouseenter="this.style.background='oklch(0.52 0.13 168)';"
                onmouseleave="this.style.background='var(--accent)';">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M15 10l4.553-2.276A1 1 0 0121 8.723v6.554a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                </svg>
                Generate Video Script
              </button>
            </div>
          </div>
        </form>
      </div>

      {{-- Hint text --}}
      <p id="hint-text" style="text-align:center;margin-top:12px;font-size:12px;color:var(--text-3);display:none;">
        Add a bit more content to get started (at least 20 characters)
      </p>

      {{-- Demo shortcuts --}}
      <div id="demo-shortcuts" style="margin-top:28px;display:flex;justify-content:center;gap:16px;flex-wrap:wrap;">
        <button onclick="fillDemo('notes')" class="demo-btn">📋 Paste meeting notes</button>
        <button onclick="fillDemo('article')" class="demo-btn">📄 Drop an article</button>
        <button onclick="fillDemo('ideas')" class="demo-btn">💡 Dump your ideas</button>
      </div>

    </div>
  </main>
</div>
@endsection

@push('scripts')
<style>
.demo-btn {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 8px;
  padding: 7px 14px;
  font-size: 12px;
  color: var(--text-2);
  cursor: pointer;
  font-family: var(--font-body);
  transition: all .15s;
  font-weight: 500;
}
.demo-btn:hover { border-color: var(--accent); color: var(--accent); }
</style>
<script>
const demos = {
  notes: `Q2 Product Review Meeting – Key Takeaways\n\nWe discussed the new onboarding flow redesign. The current drop-off rate is 42% at step 3. Main issues: too many fields, no progress indicator, and unclear value prop on the first screen.\n\nDecisions made:\n- Reduce onboarding steps from 7 to 4\n- Add a progress bar component\n- A/B test two versions of the welcome screen\n- Ship by end of Q2\n\nNext steps: Design team to deliver mockups by Friday. Engineering to scope by next Monday. PM to draft success metrics this week.`,
  article: `The Rise of AI-Powered Productivity Tools\n\nOver the past two years, AI tools have transformed how knowledge workers operate. From writing assistants to code generators, the landscape is shifting rapidly. Companies that adopt these tools report 30-40% productivity gains on average.\n\nKey trends driving adoption:\n1. Improved language models with better context understanding\n2. Lower API costs making integration affordable for startups\n3. User expectations rising — people want instant results\n4. No-code interfaces democratizing access\n\nThe challenge now isn't capability — it's trust and workflow integration. Users need tools that fit naturally into their existing processes without disrupting team dynamics.`,
  ideas: `Ideas for the new feature launch:\n\nWe should make onboarding feel magical — like the product already knows what you need. Start with a simple question: what do you want to accomplish? Then guide users to the exact feature.\n\nGamification could help with activation. Give users a checklist with small wins early on. Confetti on first completion. Progress bar visible always.\n\nPricing page needs a total rethink. Too much text. People want to see value, not features. Use before/after comparisons. Add video testimonials. Show the ROI calculator upfront.\n\nMobile experience is lagging. 60% of signups happen on mobile but conversion is half of desktop. Need responsive forms, better touch targets, and a dedicated mobile onboarding path.`
};

const textarea = document.getElementById('content-input');
const charCount = document.getElementById('char-count');
const hintText = document.getElementById('hint-text');
const demoShortcuts = document.getElementById('demo-shortcuts');
const btnSlides = document.getElementById('btn-slides');
const btnVideo = document.getElementById('btn-video');

function updateUI() {
  const len = textarea.value.length;
  charCount.textContent = len.toLocaleString() + ' chars';
  charCount.style.color = len > 5000 ? '#E05252' : 'var(--text-3)';

  const canSubmit = textarea.value.trim().length > 20;
  hintText.style.display = (len > 0 && !canSubmit) ? 'block' : 'none';
  demoShortcuts.style.display = len === 0 ? 'flex' : 'none';

  btnSlides.disabled = !canSubmit;
  btnVideo.disabled = !canSubmit;
  if (!canSubmit) {
    btnSlides.style.opacity = '0.5';
    btnSlides.style.cursor = 'not-allowed';
    btnVideo.style.opacity = '0.5';
    btnVideo.style.cursor = 'not-allowed';
  } else {
    btnSlides.style.opacity = '1';
    btnSlides.style.cursor = 'pointer';
    btnVideo.style.opacity = '1';
    btnVideo.style.cursor = 'pointer';
  }
}

function fillDemo(key) {
  textarea.value = demos[key];
  updateUI();
  textarea.focus();
}

textarea.addEventListener('input', updateUI);
updateUI();

// Loading overlay logic
const overlay = document.getElementById('loading-overlay');
const loadingStep = document.getElementById('loading-step');
const loadingSub = document.getElementById('loading-sub');
const loadingBar = document.getElementById('loading-bar');
const loadingIcon = document.getElementById('loading-icon');

const slidesIcon = `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>`;
const videoIcon  = `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M15 10l4.553-2.276A1 1 0 0121 8.723v6.554a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/></svg>`;

document.getElementById('generate-form').addEventListener('submit', function(e) {
  const clicked = document.activeElement;
  if (!clicked || clicked.disabled) { e.preventDefault(); return; }

  const mode = clicked.value;
  const steps = mode === 'slides'
    ? ['Reading your content…', 'Identifying key ideas…', 'Structuring slides…', 'Almost there…']
    : ['Reading your content…', 'Finding the narrative arc…', 'Writing the script…', 'Polishing tone…'];

  loadingIcon.innerHTML = mode === 'slides' ? slidesIcon : videoIcon;
  loadingSub.textContent = 'Generating your ' + (mode === 'slides' ? 'presentation' : 'video script');
  overlay.classList.add('visible');

  let step = 0;
  loadingStep.textContent = steps[0];
  loadingBar.style.width = '25%';

  const interval = setInterval(() => {
    step = Math.min(step + 1, steps.length - 1);
    loadingStep.textContent = steps[step];
    loadingBar.style.width = ((step + 1) / steps.length * 100) + '%';
    if (step === steps.length - 1) clearInterval(interval);
  }, 1100);
});
</script>
@endpush
