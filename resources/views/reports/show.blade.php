@php
    $audienceLabels = [
        'internal' => 'Internal Report',
        'bduk' => 'Project Report - Boeing Defence UK (BDUK)',
        'mod' => 'Project Report - Ministry of Defence (MOD)',
    ];

    $recipientLines = [
        'internal' => [
            'TransFlo Instruments',
            'GFMS Version 8 Implementation',
        ],
        'bduk' => [
            'Boeing Information Services | Boeing Defence UK Limited | The Boeing Company',
            'Project: GFMS Version 8 Implementation - Project Manager – MoD IS Programme',
        ],
        'mod' => [
            'Service Transition Enterprise Team Lead',
            'IT Service Manager | Data & Digital',
            'DE&S | Logistics, Services & Commodities | Support Live Service Operations',
            'Project: GFMS Version 8 Implementation - Project Manager – MoD IS Programme',
        ],
    ];

    $audienceTitle = $audienceLabels[$audience] ?? 'Implementation Report';
    $lines = $recipientLines[$audience] ?? [];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $audienceTitle }}</title>

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
                    <a href="{{ route('reports.' . $audience . '.csv') }}" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700">CSV</a>
                    <button onclick="window.print()" class="rounded-md bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white">Print / Save PDF</button>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('images/transflo-logo.jpg') }}" alt="TransFlo Instruments" class="h-12 w-auto object-contain" />
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.4em] text-slate-400">TransFlo Instruments</p>
                            <h1 class="mt-2 text-2xl font-semibold">{{ $audienceTitle }}</h1>
                        </div>
                    </div>
                    <div class="text-right text-xs text-slate-500">
                        <p>Generated: {{ $generatedAt->format('Y-m-d H:i') }}</p>
                        <p>Report Version: 1.0</p>
                    </div>
                </div>

                <div class="mt-6 border-t border-slate-200 pt-6 text-sm text-slate-700">
                    @foreach ($lines as $line)
                        <p>{{ $line }}</p>
                    @endforeach
                </div>

                <div class="mt-8 grid gap-4 md:grid-cols-3">
                    <div class="rounded-xl border border-slate-200 p-4">
                        <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">Total Sites</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $totalPlants }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 p-4">
                        <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">Completed</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $completedPlants }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 p-4">
                        <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">Pending</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $pendingPlants }}</p>
                    </div>
                </div>

                <div class="mt-6">
                    <div class="flex items-center justify-between text-sm text-slate-600">
                        <span>Completion Progress</span>
                        <span class="font-semibold text-slate-900">{{ $completionPercent }}%</span>
                    </div>
                    <div class="mt-2 h-3 w-full rounded-full bg-slate-100">
                        <div class="h-3 rounded-full bg-slate-900" style="width: {{ $completionPercent }}%"></div>
                    </div>
                </div>

                <div class="mt-8">
                    <h2 class="text-lg font-semibold">Completed Sites</h2>
                    <div class="mt-3 overflow-x-auto rounded-xl border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">Plant ID</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">Site Name</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">Implemented By</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @foreach ($plants as $plant)
                                    @if ($plant->implementationCompletion)
                                        <tr>
                                            <td class="px-4 py-2">{{ $plant->plant_id }}</td>
                                            <td class="px-4 py-2">{{ $plant->site_name }}</td>
                                            <td class="px-4 py-2">{{ $plant->implementationCompletion?->user?->name ?? '—' }}</td>
                                            <td class="px-4 py-2">{{ $plant->implementationCompletion?->implemented_at?->format('Y-m-d') ?? '—' }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-8">
                    <h2 class="text-lg font-semibold">Pending Sites</h2>
                    <div class="mt-3 overflow-x-auto rounded-xl border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">Plant ID</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">Site Name</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @foreach ($plants as $plant)
                                    @if (! $plant->implementationCompletion)
                                        <tr>
                                            <td class="px-4 py-2">{{ $plant->plant_id }}</td>
                                            <td class="px-4 py-2">{{ $plant->site_name }}</td>
                                            <td class="px-4 py-2 text-slate-500">Pending</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="no-print mt-4 text-xs text-slate-500">
                PDF output uses your browser print dialog. Choose “Save as PDF”.
            </div>
        </div>
    </div>
</body>
</html>
