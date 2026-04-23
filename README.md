# masaq LM

A minimal AI-powered platform that transforms raw content — pasted text or a PDF — into presentation slides or a video script.

## Features

- **Text input** — paste notes, articles, or ideas directly
- **PDF upload** — drag-and-drop or browse, up to 5 MB
- Four tone options: Professional, Casual, Academic, Storytelling
- Animated loading screen with step-by-step progress
- Slides output: numbered cards with bullet points and visual hints
- Video script output: color-coded sections with stage directions
- Copy-to-clipboard for all output
- Source badge on results (Text / PDF)
- Mock mode — fully functional with no API key

## Requirements

- PHP 8.2+
- Composer
- Node.js 18+

## Setup

### 1. Install PHP dependencies

```bash
composer install
```

This installs all packages including `smalot/pdfparser` for PDF text extraction.

### 2. Install frontend dependencies

```bash
npm install
```

### 3. Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure AI (optional)

Open `.env` and add your Anthropic API key:

```
ANTHROPIC_API_KEY=sk-ant-...
```

Leave it blank to use built-in mock data — no API key needed for the demo.

### 5. Run the app

**Two terminals:**

```bash
# Terminal 1 — Vite asset server
npm run dev

# Terminal 2 — Laravel app (or use Laravel Herd)
php artisan serve
```

Then open **[http://localhost:8000](http://localhost:8000)**.

> **Using Laravel Herd?** Skip `php artisan serve` — Herd serves the app automatically at `http://masaq-lm.test`.

## PDF support notes

- Supports text-based PDFs only (not scanned images)
- Max file size: 5 MB
- Content is trimmed to ~8 000 characters before AI processing to avoid token limits
- Uses [smalot/pdfparser](https://github.com/smalot/pdfparser) for extraction

## Tech stack

- **Laravel 13** — MVC, routing, session, HTTP client
- **Blade** — server-rendered templates
- **Tailwind CSS v4** — utility styles
- **smalot/pdfparser** — PDF text extraction
- **Claude Sonnet 4.6** — AI generation via Anthropic API

## Architecture

```
app/
  Http/Controllers/ContentController.php   — index, generate, result
  Services/AiContentService.php            — Anthropic API + mock fallback
  Services/PdfParserService.php            — PDF text extraction + cleanup
resources/views/
  layouts/app.blade.php                    — base layout + loading overlay
  input.blade.php                          — text/PDF input with tab switcher
  result.blade.php                         — slides or video script output
routes/web.php                             — GET /, POST /generate, GET /result
```

## Routes

| Method | Path        | Description          |
|--------|-------------|----------------------|
| GET    | `/`         | Input form           |
| POST   | `/generate` | Process and generate |
| GET    | `/result`   | Display output       |
