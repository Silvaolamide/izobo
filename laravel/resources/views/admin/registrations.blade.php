<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IZOBO Registrations</title>
    <style>
        body { margin: 0; background: #f7f7f2; color: #10231c; font-family: Arial, sans-serif; }
        .wrap { width: min(1400px, calc(100% - 30px)); margin: auto; }
        .head { padding: 25px 0; display: flex; justify-content: space-between; align-items: center; gap: 15px; }
        .brand { font-weight: 900; font-size: 22px; color: #075b40; }
        .brand span { color: #ef3037; }
        .note { color: #64736d; font-size: 12px; line-height: 1.6; }
        .actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .btn { display: inline-flex; align-items: center; justify-content: center; text-decoration: none; border: 0; border-radius: 999px; padding: 10px 16px; font-weight: 800; cursor: pointer; font-size: 13px; }
        .primary { background: #075b40; color: #fff; }
        .secondary { background: #fff; color: #075b40; border: 1px solid #d6e0db; }
        .panel { background: #fff; border: 1px solid #e1e8e4; border-radius: 16px; padding: 20px; margin-bottom: 18px; }
        .filters { display: grid; grid-template-columns: 2fr repeat(4, 1fr) auto; gap: 10px; align-items: end; }
        label { display: block; font-size: 11px; font-weight: 800; color: #46564f; margin-bottom: 6px; }
        input, select { width: 100%; box-sizing: border-box; border: 1px solid #d6e0db; border-radius: 10px; padding: 11px 12px; background: #fff; font: inherit; font-size: 13px; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 1050px; }
        th, td { padding: 12px 10px; border-bottom: 1px solid #edf0ee; text-align: left; font-size: 12px; vertical-align: top; }
        th { background: #f4f7f5; color: #46564f; text-transform: uppercase; letter-spacing: .06em; font-size: 10px; white-space: nowrap; }
        tr:hover td { background: #fafcfb; }
        .name { font-weight: 800; color: #10231c; }
        .count { font-size: 13px; font-weight: 800; color: #075b40; margin-bottom: 12px; }
        .pagination { display: flex; justify-content: space-between; align-items: center; gap: 15px; margin-top: 18px; flex-wrap: wrap; }
        .pagination a, .pagination span { display: inline-flex; padding: 8px 11px; border: 1px solid #d6e0db; border-radius: 8px; text-decoration: none; color: #075b40; font-size: 12px; }
        .pagination .disabled { color: #9aa7a1; background: #f7f8f7; }
        .pages { display: flex; gap: 5px; flex-wrap: wrap; }
        .empty { padding: 45px 20px; text-align: center; color: #64736d; }
        @media (max-width: 900px) { .filters { grid-template-columns: 1fr 1fr; } .filters .search { grid-column: 1 / -1; } }
        @media (max-width: 600px) { .filters { grid-template-columns: 1fr; } .filters .search { grid-column: auto; } .head { align-items: flex-start; flex-direction: column; } }
    </style>
</head>
<body>
<div class="wrap">
    <header class="head">
        <div>
            <div class="brand">IZOBO<span>.</span></div>
            <div class="note">All campaign registrations</div>
        </div>
        <div class="actions">
            <a class="btn secondary" href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a class="btn primary" href="{{ route('admin.registrations.export', request()->query()) }}">Export CSV</a>
        </div>
    </header>

    <section class="panel">
        <form method="GET" action="{{ route('admin.registrations') }}" class="filters">
            <div class="search">
                <label for="search">Search</label>
                <input id="search" name="search" value="{{ request('search') }}" placeholder="Name, phone or email">
            </div>

            <div>
                <label for="state_id">State</label>
                <select id="state_id" name="state_id">
                    <option value="">All states</option>
                    @foreach ($states as $state)
                        <option value="{{ $state->id }}" @selected((string) request('state_id') === (string) $state->id)>{{ $state->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="lga_id">LGA</label>
                <select id="lga_id" name="lga_id">
                    <option value="">All LGAs</option>
                    @foreach ($lgas as $lga)
                        <option value="{{ $lga->id }}" data-state="{{ $lga->state_id }}" @selected((string) request('lga_id') === (string) $lga->id)>{{ $lga->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="ward_id">Ward</label>
                <select id="ward_id" name="ward_id">
                    <option value="">All wards</option>
                    @foreach ($wards as $ward)
                        <option value="{{ $ward->id }}" data-lga="{{ $ward->lga_id }}" @selected((string) request('ward_id') === (string) $ward->id)>{{ $ward->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="interest">Interest</label>
                <select id="interest" name="interest">
                    <option value="">All interests</option>
                    @foreach (['Volunteer','Campaign supporter','Community outreach','Digital/media support','Other'] as $interest)
                        <option value="{{ $interest }}" @selected(request('interest') === $interest)>{{ $interest }}</option>
                    @endforeach
                </select>
            </div>

            <button class="btn primary" type="submit">Filter</button>
        </form>
    </section>

    <section class="panel">
        <div class="count">
            Showing {{ $registrations->firstItem() ?? 0 }}–{{ $registrations->lastItem() ?? 0 }} of {{ number_format($registrations->total()) }} registrations
        </div>

        @if ($registrations->isEmpty())
            <div class="empty">No registrations match your search or filters.</div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Registered</th>
                        <th>Full name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>State</th>
                        <th>LGA</th>
                        <th>Ward</th>
                        <th>Interest</th>
                        <th>Consent</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($registrations as $registration)
                        <tr>
                            <td>{{ $registration->id }}</td>
                            <td>{{ optional($registration->created_at)->format('d M Y, h:i A') }}</td>
                            <td class="name">{{ $registration->full_name }}</td>
                            <td>{{ $registration->phone }}</td>
                            <td>{{ $registration->email ?: '—' }}</td>
                            <td>{{ $registration->state?->name ?: '—' }}</td>
                            <td>{{ $registration->lga?->name ?: '—' }}</td>
                            <td>{{ $registration->ward?->name ?: '—' }}</td>
                            <td>{{ $registration->interest }}</td>
                            <td>{{ $registration->consent ? 'Yes' : 'No' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                <div class="note">25 registrations per page</div>
                <div class="pages">
                    @if ($registrations->onFirstPage())
                        <span class="disabled">Previous</span>
                    @else
                        <a href="{{ $registrations->previousPageUrl() }}">Previous</a>
                    @endif

                    @foreach ($registrations->getUrlRange(max(1, $registrations->currentPage() - 2), min($registrations->lastPage(), $registrations->currentPage() + 2)) as $page => $url)
                        @if ($page == $registrations->currentPage())
                            <span style="background:#075b40;color:#fff">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($registrations->hasMorePages())
                        <a href="{{ $registrations->nextPageUrl() }}">Next</a>
                    @else
                        <span class="disabled">Next</span>
                    @endif
                </div>
            </div>
        @endif
    </section>
</div>

<script>
    const state = document.getElementById('state_id');
    const lga = document.getElementById('lga_id');
    const ward = document.getElementById('ward_id');

    function filterLgas() {
        const selectedState = state.value;
        [...lga.options].forEach(option => {
            if (!option.value) return;
            option.hidden = selectedState && option.dataset.state !== selectedState;
        });
        if (lga.selectedOptions[0]?.hidden) lga.value = '';
        filterWards();
    }

    function filterWards() {
        const selectedLga = lga.value;
        [...ward.options].forEach(option => {
            if (!option.value) return;
            option.hidden = selectedLga && option.dataset.lga !== selectedLga;
        });
        if (ward.selectedOptions[0]?.hidden) ward.value = '';
    }

    state.addEventListener('change', filterLgas);
    lga.addEventListener('change', filterWards);
    filterLgas();
</script>
</body>
</html>
