<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'Weather Queue App') }}</title>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
            body {
                font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                margin: 0;
                padding: 0;
                background: #0f172a;
                color: #e5e7eb;
            }
            .page {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 1.5rem;
            }
            .card {
                width: 100%;
                max-width: 900px;
                background: #020617;
                border-radius: 1rem;
                box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.8),
                            0 8px 10px -6px rgba(15, 23, 42, 0.6);
                border: 1px solid rgba(148, 163, 184, 0.3);
                display: grid;
                grid-template-columns: minmax(0, 1.3fr) minmax(0, 1fr);
                overflow: hidden;
            }
            .card-main {
                padding: 2rem 2.25rem;
            }
            .card-side {
                background: radial-gradient(circle at top, #1d4ed8, #020617 55%);
                color: #e5e7eb;
                padding: 2rem;
                border-left: 1px solid rgba(148, 163, 184, 0.3);
                display: flex;
                flex-direction: column;
                justify-content: space-between;
            }
            .badge {
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
                border-radius: 999px;
                border: 1px solid rgba(96, 165, 250, 0.6);
                padding: 0.15rem 0.7rem;
                font-size: 0.75rem;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                color: #bfdbfe;
                background: rgba(15, 23, 42, 0.7);
            }
            .badge-dot {
                width: 0.5rem;
                height: 0.5rem;
                border-radius: 999px;
                background: #22c55e;
                box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.3);
            }
            h1 {
                font-size: 1.6rem;
                margin: 1rem 0 0.35rem;
                font-weight: 600;
                letter-spacing: -0.02em;
            }
            .subtitle {
                color: #9ca3af;
                font-size: 0.95rem;
                margin-bottom: 1.5rem;
            }
            a {
                color: #60a5fa;
            }
            .flash {
                padding: 0.6rem 0.85rem;
                border-radius: 0.75rem;
                margin-bottom: 1rem;
                font-size: 0.9rem;
                display: flex;
                align-items: flex-start;
                gap: 0.5rem;
            }
            .flash-success {
                background: rgba(22, 163, 74, 0.12);
                border: 1px solid rgba(52, 211, 153, 0.3);
                color: #bbf7d0;
            }
            .flash-error {
                background: rgba(220, 38, 38, 0.15);
                border: 1px solid rgba(248, 113, 113, 0.4);
                color: #fecaca;
            }
            .flash-icon {
                margin-top: 0.1rem;
                font-weight: bold;
            }
            .content {
                margin-top: 0.5rem;
            }
            .footer {
                margin-top: 2rem;
                font-size: 0.8rem;
                color: #6b7280;
                border-top: 1px solid rgba(31, 41, 55, 0.8);
                padding-top: 0.8rem;
            }
            .footer strong {
                color: #e5e7eb;
            }
            .side-title {
                font-size: 0.95rem;
                text-transform: uppercase;
                letter-spacing: 0.16em;
                color: #bfdbfe;
                margin-bottom: 0.5rem;
            }
            .side-heading {
                font-size: 1.3rem;
                font-weight: 600;
                margin-bottom: 0.5rem;
            }
            .side-text {
                font-size: 0.9rem;
                color: #cbd5f5;
                margin-bottom: 1.2rem;
            }
            .pill-row {
                display: flex;
                flex-wrap: wrap;
                gap: 0.35rem;
            }
            .pill {
                font-size: 0.75rem;
                padding: 0.2rem 0.65rem;
                border-radius: 999px;
                background: rgba(15, 23, 42, 0.7);
                border: 1px solid rgba(148, 163, 184, 0.5);
                color: #e5e7eb;
            }
            .pill-strong {
                background: linear-gradient(135deg, #1d4ed8, #22c55e);
                border-color: transparent;
                color: white;
            }
            @media (max-width: 768px) {
                .card {
                    grid-template-columns: minmax(0, 1fr);
                }
                .card-side {
                    border-left: none;
                    border-top: 1px solid rgba(148, 163, 184, 0.3);
                }
            }
        </style>
    @endif
</head>
<body>
<div class="page">
    <div class="card">
        <div class="card-main">
            <div class="badge">
                <span class="badge-dot"></span>
                <span>Queued Weather Demo</span>
            </div>

            <h1>{{ $heading ?? 'Weather data in the background' }}</h1>
            <p class="subtitle">
                Enter a city, we queue a background job to fetch and format the weather JSON,
                then store the result for you to explore.
            </p>

            @if (session('status'))
                <div class="flash flash-success">
                    <span class="flash-icon">✓</span>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="flash flash-error">
                    <span class="flash-icon">!</span>
                    <div>
                        <strong>There was a problem with your input:</strong>
                        <ul style="margin: 0.35rem 0 0 1rem; padding: 0; list-style: disc;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="content">
                @yield('content')
            </div>

            <div class="footer">
                <div><strong>Dilwar Loshkor Weather Application</strong></div>
            </div>
        </div>

        <aside class="card-side">
            <div>
                <div class="side-title">Architecture Snapshot</div>
                <div class="side-heading">Laravel + Queues + Weather API</div>
                <p class="side-text">
                    Requests are stored in the <code>weather_requests</code> table,
                    then processed by a queued <code>FetchAndProcessWeatherJob</code>.
                </p>
                <div class="pill-row">
                    <span class="pill pill-strong">Queued Job</span>
                    <span class="pill">Database queue driver</span>
                    <span class="pill">Background processing</span>
                    <span class="pill">External API</span>
                    <span class="pill">Status tracking</span>
                </div>
            </div>
        </aside>
    </div>
</div>
</body>
</html>

