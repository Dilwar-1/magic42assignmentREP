@extends('layouts.app')

@section('content')
    @php use App\Support\WeatherRequestPresenter as WeatherView; @endphp

    <form action="{{ route('weather.store') }}" method="POST" style="margin-bottom: 1.75rem;">
        @csrf
        <label for="location" style="display:block; font-size:0.9rem; font-weight:500; margin-bottom:0.35rem;">
            City or location
        </label>
        <div style="display:flex; gap:0.5rem; align-items:center;">
            <input
                type="text"
                id="location"
                name="location"
                value="{{ old('location') }}"
                placeholder="e.g. London, Tokyo, New York"
                style="
                    flex:1;
                    padding:0.55rem 0.7rem;
                    border-radius:0.65rem;
                    border:1px solid rgba(55,65,81,0.8);
                    background:#020617;
                    color:#e5e7eb;
                    font-size:0.9rem;
                "
            >
            <button
                type="submit"
                style="
                    padding:0.55rem 1.1rem;
                    border-radius:999px;
                    border:0;
                    background:linear-gradient(135deg, #3b82f6, #22c55e);
                    color:white;
                    font-size:0.9rem;
                    font-weight:500;
                    cursor:pointer;
                    white-space:nowrap;
                "
            >
                Queue weather fetch
            </button>
        </div>
        <p style="margin-top:0.4rem; font-size:0.8rem; color:#9ca3af;">
            This does not call the API directly in the request. It creates a record and
            dispatches a queued job to fetch and format the JSON in the background.
        </p>
    </form>

    <div>
        <h2 style="font-size:1rem; font-weight:500; margin-bottom:0.7rem;">
            Recent weather requests
        </h2>

        @if ($recentRequests->isEmpty())
            <p style="font-size:0.9rem; color:#9ca3af;">
                No weather requests yet. Try submitting a city above.
            </p>
        @else
            <div style="border-radius:0.9rem; border:1px solid rgba(55,65,81,0.9); overflow:hidden;">
                <table style="width:100%; border-collapse:collapse; font-size:0.9rem;">
                    <thead style="background:rgba(15,23,42,0.9); text-align:left; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.08em; color:#9ca3af;">
                        <tr>
                            <th style="padding:0.6rem 0.9rem;">Location</th>
                            <th style="padding:0.6rem 0.9rem;">Status</th>
                            <th style="padding:0.6rem 0.9rem;">Temperature</th>
                            <th style="padding:0.6rem 0.9rem;">Updated</th>
                            <th style="padding:0.6rem 0.9rem;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentRequests as $request)
                            <tr style="border-top:1px solid rgba(31,41,55,0.9);">
                                <td style="padding:0.55rem 0.9rem;">
                                    {{ $request->location }}
                                </td>
                                <td style="padding:0.55rem 0.9rem;">
                                    <span style="
                                        display:inline-flex;
                                        align-items:center;
                                        gap:0.35rem;
                                        font-size:0.8rem;
                                    ">
                                        <span style="
                                            width:0.45rem;
                                            height:0.45rem;
                                            border-radius:999px;
                                            background:{{ WeatherView::statusColor($request->status) }};
                                        "></span>
                                        <span style="text-transform:capitalize;">
                                            {{ $request->status }}
                                        </span>
                                    </span>
                                </td>
                                <td style="padding:0.55rem 0.9rem; color:#e5e7eb;">
                                    {{ $request->temperature ?? '—' }}
                                </td>
                                <td style="padding:0.55rem 0.9rem; color:#9ca3af;">
                                    {{ $request->updated_at?->diffForHumans() ?? '—' }}
                                </td>
                                <td style="padding:0.55rem 0.9rem; text-align:right;">
                                    <a
                                        href="{{ route('weather.show', $request) }}"
                                        style="
                                            font-size:0.8rem;
                                            color:#60a5fa;
                                            text-decoration:none;
                                        "
                                    >
                                        View details →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection

