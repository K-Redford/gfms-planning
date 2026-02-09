@php
    $siteTime = null;
    if ($report->site_time_minutes) {
        $hours = intdiv($report->site_time_minutes, 60);
        $minutes = $report->site_time_minutes % 60;
        $siteTime = sprintf('%dh %02dm', $hours, $minutes);
    }
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Service Report Form') }} #{{ $report->serial_number }}
            </h2>
            <a href="{{ route('service-reports.index') }}" class="rounded-md border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-700">Back</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-200 pb-4">
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('images/transflo-logo.jpg') }}" alt="TransFlo Instruments" class="h-12 w-auto object-contain" />
                        <div class="text-sm text-gray-600">
                            <p class="font-semibold text-gray-800">TransFlo Instruments Ltd</p>
                            <p>Unit 6, Rose Lane Industrial Estate, Lenham Heath, Kent ME17 2JN, UK</p>
                            <p>Telephone: +44 (0)1622 859564 · Email: servicedesk@transflo.co.uk · www.transflo.co.uk</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs uppercase tracking-widest text-gray-500">Serial</p>
                        <p class="text-2xl font-semibold text-gray-800">{{ $report->serial_number }}</p>
                    </div>
                </div>

                <h3 class="mt-4 text-center text-2xl font-semibold text-gray-800">Service Report Form</h3>

                <div class="mt-6 grid gap-4 md:grid-cols-3">
                    <div class="md:col-span-2">
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">Company</p>
                        <p class="mt-1 text-sm text-gray-800">{{ $report->company_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">GFMS Plant ID</p>
                        <p class="mt-1 text-sm text-gray-800">{{ $report->plant?->plant_id }}</p>
                    </div>
                </div>

                <div class="mt-4">
                    <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">Site Address</p>
                    <p class="mt-1 text-sm text-gray-800">{{ $report->site_address ?? '—' }}</p>
                </div>

                <div class="mt-4 grid gap-4 md:grid-cols-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">Date of Visit</p>
                        <p class="mt-1 text-sm text-gray-800">{{ $report->date_of_visit?->format('Y-m-d') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">Time on Site</p>
                        <p class="mt-1 text-sm text-gray-800">{{ $report->time_on_site ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">Time off Site</p>
                        <p class="mt-1 text-sm text-gray-800">{{ $report->time_off_site ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">Site Time</p>
                        <p class="mt-1 text-sm text-gray-800">{{ $siteTime ?? '—' }}</p>
                    </div>
                </div>

                <div class="mt-4 grid gap-4 md:grid-cols-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">Mileage</p>
                        <p class="mt-1 text-sm text-gray-800">{{ $report->mileage ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">Travel Time (hrs)</p>
                        <p class="mt-1 text-sm text-gray-800">{{ $report->travel_time_hours ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">Order No.</p>
                        <p class="mt-1 text-sm text-gray-800">{{ $report->order_number ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">TransFlo Ref</p>
                        <p class="mt-1 text-sm text-gray-800">{{ $report->transflo_ref ?? '—' }}</p>
                    </div>
                </div>

                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">Charge Type</p>
                        <p class="mt-1 text-sm text-gray-800">{{ $report->charge_type }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">Equipment Type</p>
                        <p class="mt-1 text-sm text-gray-800">{{ $report->equipment_type }}</p>
                    </div>
                </div>

                <div class="mt-4">
                    <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">Reported Fault</p>
                    <p class="mt-1 text-sm text-gray-800">{{ $report->reported_fault ?? '—' }}</p>
                </div>

                <div class="mt-4">
                    <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">Report Details</p>
                    <p class="mt-1 whitespace-pre-line text-sm text-gray-800">{{ $report->report_details ?? '—' }}</p>
                </div>
            </div>

            <div class="mt-6 bg-white shadow-sm sm:rounded-lg p-6">
                <div class="grid gap-6 md:grid-cols-3">
                    <div>
                        <h4 class="text-sm font-semibold uppercase tracking-widest text-gray-500">Departure Status</h4>
                        <div class="mt-3 space-y-2 text-sm text-gray-800">
                            @forelse ($report->departure_statuses ?? [] as $status)
                                <p>• {{ $status }}</p>
                            @empty
                                <p class="text-gray-500">—</p>
                            @endforelse
                        </div>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold uppercase tracking-widest text-gray-500">Software Changes</h4>
                        <p class="mt-3 text-sm text-gray-800">{{ $report->software_changes ?? '—' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold uppercase tracking-widest text-gray-500">Engineer Signature</h4>
                        <div class="mt-3">
                            @if ($report->engineer_signature_path)
                                <img src="{{ asset('storage/' . $report->engineer_signature_path) }}" alt="Engineer Signature" class="h-24 w-auto border border-gray-200" />
                            @else
                                <p class="text-sm text-gray-500">—</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 bg-white shadow-sm sm:rounded-lg p-6">
                <h4 class="text-sm font-semibold uppercase tracking-widest text-gray-500">Parts Supplied</h4>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-xs uppercase tracking-widest text-gray-500">
                            <tr>
                                <th class="px-2 py-2 text-left">Part Description</th>
                                <th class="px-2 py-2 text-left">Stock Code</th>
                                <th class="px-2 py-2 text-left">No. Off</th>
                                <th class="px-2 py-2 text-left">Price Each</th>
                                <th class="px-2 py-2 text-left">Total Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($report->parts as $part)
                                <tr class="border-t border-gray-200">
                                    <td class="px-2 py-2">{{ $part->part_description ?? '—' }}</td>
                                    <td class="px-2 py-2">{{ $part->stock_code ?? '—' }}</td>
                                    <td class="px-2 py-2">{{ $part->quantity ?? '—' }}</td>
                                    <td class="px-2 py-2">{{ $part->price_each ?? '—' }}</td>
                                    <td class="px-2 py-2">{{ $part->total_price ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr class="border-t border-gray-200">
                                    <td class="px-2 py-2 text-gray-500" colspan="5">No parts recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6 bg-white shadow-sm sm:rounded-lg p-6">
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <h4 class="text-sm font-semibold uppercase tracking-widest text-gray-500">Customer Signature</h4>
                        <div class="mt-3">
                            @if ($report->customer_signature_path)
                                <img src="{{ asset('storage/' . $report->customer_signature_path) }}" alt="Customer Signature" class="h-24 w-auto border border-gray-200" />
                            @else
                                <p class="text-sm text-gray-500">—</p>
                            @endif
                        </div>
                    </div>
                    <div class="space-y-2 text-sm text-gray-800">
                        <p><span class="text-xs font-semibold uppercase tracking-widest text-gray-500">Print Name:</span> {{ $report->customer_print_name ?? '—' }}</p>
                        <p><span class="text-xs font-semibold uppercase tracking-widest text-gray-500">Rank/Civ:</span> {{ $report->customer_rank_civ ?? '—' }}</p>
                        <p><span class="text-xs font-semibold uppercase tracking-widest text-gray-500">Customer Email:</span> {{ $report->customer_email ?? '—' }}</p>
                    </div>
                </div>

                <div class="mt-6">
                    <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">Notes</p>
                    <p class="mt-1 whitespace-pre-line text-sm text-gray-800">{{ $report->notes ?? '—' }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
