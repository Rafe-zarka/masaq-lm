<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'masaq LM')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap" rel="stylesheet" />
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
  <div id="app">
    @yield('content')
  </div>

  {{-- Loading overlay (shown on form submit) --}}
  <div id="loading-overlay" aria-live="polite" aria-label="Generating content">
    <div style="width:64px;height:64px;border-radius:18px;background:var(--accent-soft);border:1px solid var(--accent-mid);display:flex;align-items:center;justify-content:center;" id="loading-icon">
      {{-- icon injected by JS --}}
    </div>
    <div style="text-align:center;">
      <div class="dots" style="justify-content:center;margin-bottom:16px;">
        <span></span><span></span><span></span>
      </div>
      <p id="loading-step" style="font-family:var(--font-head);font-weight:600;font-size:17px;color:var(--text);margin-bottom:6px;">Generating…</p>
      <p id="loading-sub" style="color:var(--text-3);font-size:13px;"></p>
    </div>
    <div style="width:240px;height:3px;background:var(--border);border-radius:2px;overflow:hidden;">
      <div id="loading-bar" style="height:100%;border-radius:2px;background:var(--accent);width:0%;transition:width 1s cubic-bezier(.22,1,.36,1);"></div>
    </div>
  </div>

  @stack('scripts')
</body>
</html>
