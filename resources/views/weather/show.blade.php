@extends('layouts.app')

@section('content')
    <div style="margin-bottom:1.5rem;">
        <a href="{{ route('weather.index') }}" style="font-size:0.85rem; color:#9ca3af; text-decoration:none;">
            ← Back to all requests
        </a>
    </div>

    <h2 style="font-size:1.1rem; font-weight:500; margin-bottom:0.4rem;">
        Weather request for <span style="color:#bfdbfe;">{{ $weatherRequest->location }}</span>
    </h2>

    @php
        $status = $weatherRequest->status;
        $statusColor = [
            'pending' => '#f97316',
            'processing' => '#eab308',
            'completed' => '#22c55e',
            'failed' => '#ef4444',
        ][$status] ?? '#9ca3af';
    @endphp

    <p style="margin-bottom:0.8rem; font-size:0.9rem; color:#9ca3af;">
        Current status:
        <span style="display:inline-flex; align-items:center; gap:0.35rem; margin-left:0.15rem;">
            <span style="
                width:0.5rem;
                height:0.5rem;
                border-radius:999px;
                background:{{ $statusColor }};
            "></span>
            <span style="text-transform:capitalize; color:#e5e7eb;">
                {{ $status }}
            </span>
        </span>
        <span style="margin-left:0.5rem; color:#6b7280; font-size:0.8rem;">
            (last updated {{ $weatherRequest->updated_at?->diffForHumans() ?? '—' }})
        </span>
    </p>

    @if ($weatherRequest->status === 'pending' || $weatherRequest->status === 'processing')
        <div style="
            padding:0.8rem 0.9rem;
            border-radius:0.8rem;
            border:1px dashed rgba(148,163,184,0.7);
            background:rgba(15,23,42,0.6);
            font-size:0.9rem;
            margin-bottom:1.2rem;
        ">
            We have stored your request and dispatched a queued job to fetch and process
            the weather JSON. Once the job finishes, this page will show the formatted
            result. In a real app you could auto-refresh or use websockets to update
            the status live.
        </div>
    @endif

    @if ($weatherRequest->status === 'failed')
        <div style="
            padding:0.8rem 0.9rem;
            border-radius:0.8rem;
            border:1px solid rgba(248,113,113,0.5);
            background:rgba(127,29,29,0.4);
            font-size:0.9rem;
            margin-bottom:1.2rem;
        ">
            <strong>Job failed.</strong><br>
            <span style="font-size:0.85rem;">
                {{ $weatherRequest->error_message ?? 'Unknown error.' }}
            </span>
        </div>
    @endif

    @if ($weatherRequest->status === 'completed')
        <div style="display:grid; grid-template-columns:minmax(0,1.1fr) minmax(0,1fr); gap:1rem;">
            <div>
                <h3 style="font-size:0.95rem; font-weight:500; margin-bottom:0.4rem;">
                    Human‑friendly summary
                </h3>
                <div style="
                    padding:0.9rem;
                    border-radius:0.8rem;
                    border:1px solid rgba(55,65,81,0.9);
                    background:rgba(15,23,42,0.8);
                    font-size:0.95rem;
                ">
                    @php
                        $formatted = null;
                        if ($weatherRequest->formatted_data) {
                            $formatted = json_decode($weatherRequest->formatted_data, true);
                        }
                    @endphp

                    @if (is_array($formatted) && isset($formatted['summary']))
                        <p style="margin:0 0 0.4rem 0;">
                            {{ $formatted['summary'] }}
                        </p>
                        <p style="margin:0; font-size:0.85rem; color:#9ca3af;">
                            Temperature: <strong>{{ $formatted['temperature'] ?? $weatherRequest->temperature ?? 'n/a' }}</strong><br>
                            Condition: <strong>{{ $formatted['condition'] ?? $weatherRequest->condition ?? 'n/a' }}</strong>
                        </p>
                    @else
                        <p style="margin:0;">
                            {{ $weatherRequest->temperature ?? 'Temperature unknown' }} –
                            {{ $weatherRequest->condition ?? 'Condition unknown' }}
                        </p>
                    @endif
                </div>
            </div>

            <div>
                <h3 style="font-size:0.95rem; font-weight:500; margin-bottom:0.4rem;">
                    Raw JSON stored from the job
                </h3>
                <div style="
                    padding:0.9rem;
                    border-radius:0.8rem;
                    border:1px solid rgba(55,65,81,0.9);
                    background:#020617;
                    font-size:0.8rem;
                    max-height:260px;
                    overflow:auto;
                    white-space:pre;
                ">
                    @if ($weatherRequest->raw_json)
                        {{ $weatherRequest->raw_json }}
                    @else
                        <span style="color:#6b7280;">(No raw_json stored.)</span>
                    @endif
                </div>
            </div>
        </div>
    @else
        <p style="font-size:0.85rem; color:#9ca3af; margin-top:0.8rem;">
            Once the queued job finishes and marks this request as <strong>completed</strong>,
            this page will display both a human‑friendly summary and the raw JSON from
            the weather API.
        </p>
    @endif
@endsection

