<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>DSFA Tank Report</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=ibm-plex-sans:400,500,600,700|ibm-plex-mono:400,500" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
        }
    </style>
</head>
<body class="min-h-screen bg-[#f3f5f7] text-slate-900">
    <div class="min-h-screen px-6 py-10">
        <div class="mx-auto max-w-6xl">
            <div class="no-print mb-6 flex items-center justify-between">
                <a href="{{ route('reports.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-800">← Back to Reports</a>
                <div class="flex items-center gap-2">
                    <a href="{{ route('reports.dsfa-tanks.pdf', request()->query()) }}" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700">PDF</a>
                    <button onclick="window.print()" class="rounded-md bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white">Print / Save PDF</button>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('images/transflo-logo.jpg') }}" alt="TransFlo Instruments" class="h-12 w-auto object-contain" />
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.4em] text-slate-400">TransFlo Instruments</p>
                            <h1 class="mt-2 text-2xl font-semibold">DSFA Tank Report</h1>
                            <p class="mt-1 text-sm text-slate-500">Latest tank list per plant.</p>
                        </div>
                    </div>
                    <div class="text-right text-xs text-slate-500">
                        <p>Generated: {{ $generatedAt->format('Y-m-d H:i') }}</p>
                        <p>Report Version: 1.0</p>
                    </div>
                </div>

                <div class="no-print mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <form method="GET" class="flex flex-wrap items-end gap-3">
                        <div class="flex flex-col">
                            <label class="text-xs font-semibold uppercase tracking-widest text-slate-500">Plant ID Filter</label>
                            <select name="plant_id" class="mt-1 w-64 rounded-md border border-slate-300 px-3 py-2 text-sm">
                                <option value="">All plants</option>
                                @foreach ($plantOptions as $plant)
                                    <option value="{{ $plant->plant_id }}" {{ (string) $plantFilter === (string) $plant->plant_id ? 'selected' : '' }}>
                                        {{ $plant->plant_id }} - {{ $plant->site_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-col">
                            <label class="text-xs font-semibold uppercase tracking-widest text-slate-500">Search</label>
                            <input type="text" id="plant-search" placeholder="Type to filter" class="mt-1 w-64 rounded-md border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <button class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Apply</button>
                        <a href="{{ route('reports.dsfa-tanks') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">Reset</a>
                    </form>
                </div>

                <div class="mt-8 space-y-6">
                    @forelse ($rows as $row)
                        <div class="rounded-xl border border-slate-200 p-4">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div>
                                    <h2 class="text-lg font-semibold">{{ $row['plant_id'] }} - {{ $row['site_name'] }}</h2>
                                    <p class="text-xs text-slate-500">Latest capture: {{ optional($row['visit_date'])->format('Y-m-d') }}</p>
                                </div>
                            </div>
                            <div class="mt-4 overflow-x-auto rounded-lg border border-slate-200">
                                <table class="min-w-full divide-y divide-slate-200 text-sm">
                                    <thead class="bg-slate-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">Tank #</th>
                                            <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">Product</th>
                                            <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">Capacity (L)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200">
                                        @forelse ($row['tanks'] as $tank)
                                            <tr>
                                                <td class="px-4 py-2">{{ $tank->tank_number ?? '-' }}</td>
                                                <td class="px-4 py-2">{{ $tank->product_name ?? $tank->product_code ?? '-' }}</td>
                                                <td class="px-4 py-2">{{ $tank->capacity_litres ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td class="px-4 py-2 text-slate-500" colspan="3">No tanks recorded.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No tank data found.</p>
                    @endforelse
                </div>
            </div>

            <div class="no-print mt-4 text-xs text-slate-500">
                PDF output uses your browser print dialog. Choose "Save as PDF".
            </div>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('plant-search');
        const plantSelect = document.querySelector('select[name="plant_id"]');

        if (searchInput && plantSelect) {
            const options = Array.from(plantSelect.options);
            searchInput.addEventListener('input', () => {
                const term = searchInput.value.trim().toLowerCase();
                options.forEach((opt) => {
                    if (!opt.value) return;
                    const text = opt.textContent.toLowerCase();
                    opt.hidden = term && !text.includes(term);
                });
            });
        }
    </script>
</body>
</html>
