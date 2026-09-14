<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IZOBO Admin Dashboard</title>
    <style>
        body { margin: 0; background: #f7f7f2; color: #10231c; font-family: Arial, sans-serif; }
        .wrap { width: min(1180px, calc(100% - 30px)); margin: auto; }
        .head { padding: 25px 0; display: flex; justify-content: space-between; align-items: center; }
        .brand { font-weight: 900; font-size: 22px; color: #075b40; }
        .brand span { color: #ef3037; }
        .logout { background: #ef3037; color: #fff; border: 0; border-radius: 999px; padding: 10px 16px; font-weight: 800; }
        .cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
        .card, .panel { background: #fff; border: 1px solid #e1e8e4; border-radius: 16px; padding: 22px; }
        .label { text-transform: uppercase; font-size: 10px; letter-spacing: .14em; color: #64736d; font-weight: 800; }
        .number { font-size: 34px; font-weight: 900; margin-top: 9px; color: #075b40; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-top: 18px; }
        .panel h2 { font-size: 18px; margin-top: 0; }
        .row { display: flex; justify-content: space-between; border-top: 1px solid #edf0ee; padding: 11px 0; font-size: 13px; }
        .bar { height: 7px; background: #eaf0ed; border-radius: 10px; overflow: hidden; margin-top: 6px; }
        .bar i { display: block; height: 100%; background: #0aa96a; border-radius: 10px; }
        .note { color: #64736d; font-size: 12px; line-height: 1.6; }
        .notice { padding: 13px 15px; border-radius: 12px; background: #edf8f0; color: #075b40; font-size: 13px; font-weight: 700; margin: 18px 0; }
        .settings { margin-top: 18px; }
        .settings form { display: grid; gap: 12px; }
        .settings label { font-size: 12px; font-weight: 800; color: #46564f; }
        .settings input { width: 100%; box-sizing: border-box; border: 1px solid #d6e0db; border-radius: 10px; padding: 13px 14px; font: inherit; }
        .settings button { justify-self: start; border: 0; border-radius: 999px; padding: 11px 18px; background: #075b40; color: #fff; font-weight: 800; cursor: pointer; }
        .error { font-size: 12px; color: #a52229; margin-top: 6px; }
        @media (max-width: 800px) { .cards { grid-template-columns: 1fr 1fr; } .grid { grid-template-columns: 1fr; } }
        @media (max-width: 500px) { .cards { grid-template-columns: 1fr; } .head { align-items: flex-start; gap: 15px; flex-direction: column; } }
    </style>
</head>
<body>
    <div class="wrap">
        <header class="head">
            <div>
                <div class="brand">IZOBO<span>.</span></div>
                <div class="note">Campaign registration overview</div>
            </div>

            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="logout">Sign out</button>
            </form>
        </header>

        <section class="cards">
            <div class="card">
                <div class="label">Total registrations</div>
                <div class="number">{{ number_format($total) }}</div>
            </div>
            <div class="card">
                <div class="label">Today</div>
                <div class="number">{{ number_format($today) }}</div>
            </div>
            <div class="card">
                <div class="label">Last 7 days</div>
                <div class="number">{{ number_format($week) }}</div>
            </div>
            <div class="card">
                <div class="label">Last 30 days</div>
                <div class="number">{{ number_format($month) }}</div>
            </div>
        </section>

        @if (session('settings_saved'))
            <div class="notice" role="status">
                {{ session('settings_saved') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="notice" style="background:#fff1f1;color:#8d2026" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <section class="panel settings">
            <h2>Campaign settings</h2>
            <p class="note">Control the WhatsApp group shown to supporters after successful registration.</p>

            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf

                <label for="whatsapp_group_link">WhatsApp group invitation link</label>
                <input
                    id="whatsapp_group_link"
                    name="whatsapp_group_link"
                    type="url"
                    value="{{ old('whatsapp_group_link', $whatsappGroupLink) }}"
                    placeholder="https://chat.whatsapp.com/your-invite-link"
                    autocomplete="off"
                >

                <button type="submit">Save WhatsApp link</button>
            </form>
        </section>

        <section class="grid">
            <div class="panel">
                <h2>Registrations by LGA</h2>
                @php($maxLga = max(1, (int) ($byLga->max('total') ?? 1)))

                @forelse ($byLga as $row)
                    <div class="row">
                        <div>
                            {{ $row->name }}
                            <div class="bar">
                                <i style="width:{{ ($row->total / $maxLga) * 100 }}%"></i>
                            </div>
                        </div>
                        <strong>{{ $row->total }}</strong>
                    </div>
                @empty
                    <p class="note">No registrations yet.</p>
                @endforelse
            </div>

            <div class="panel">
                <h2>Registrations by Ward</h2>
                @php($maxWard = max(1, (int) ($byWard->max('total') ?? 1)))

                @forelse ($byWard as $row)
                    <div class="row">
                        <div>
                            {{ $row->name }}
                            <div class="bar">
                                <i style="width:{{ ($row->total / $maxWard) * 100 }}%"></i>
                            </div>
                        </div>
                        <strong>{{ $row->total }}</strong>
                    </div>
                @empty
                    <p class="note">No registrations yet.</p>
                @endforelse
            </div>

            <div class="panel">
                <h2>Participation interest</h2>
                @php($maxInterest = max(1, (int) ($byInterest->max('total') ?? 1)))

                @forelse ($byInterest as $row)
                    <div class="row">
                        <div>
                            {{ $row->interest }}
                            <div class="bar">
                                <i style="width:{{ ($row->total / $maxInterest) * 100 }}%"></i>
                            </div>
                        </div>
                        <strong>{{ $row->total }}</strong>
                    </div>
                @empty
                    <p class="note">No registrations yet.</p>
                @endforelse
            </div>

            <div class="panel">
                <h2>Recent registration trend</h2>

                @foreach ($daily as $row)
                    <div class="row">
                        <span>{{ $row->day }}</span>
                        <strong>{{ $row->total }}</strong>
                    </div>
                @endforeach

                @if ($daily->isEmpty())
                    <p class="note">No registrations yet.</p>
                @endif
            </div>
        </section>

        <p class="note" style="margin:25px 0 50px">
            Dashboard shows aggregate registration statistics. Personal contact details are intentionally not displayed here.
        </p>
    </div>
</body>
</html>
