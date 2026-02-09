<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Service Report Forms') }}
            </h2>
            <a href="{{ route('service-reports.create') }}" class="rounded-md bg-gray-900 px-3 py-2 text-xs font-semibold text-white hover:bg-gray-800">New SRF</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-gray-500">Serial</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-gray-500">GFMS Plant ID</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-gray-500">Site Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-gray-500">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-gray-500">Engineer</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-gray-500">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($reports as $report)
                            <tr>
                                <td class="px-4 py-3">{{ $report->serial_number }}</td>
                                <td class="px-4 py-3">{{ $report->plant?->plant_id }}</td>
                                <td class="px-4 py-3">{{ $report->company_name }}</td>
                                <td class="px-4 py-3">{{ $report->date_of_visit?->format('Y-m-d') }}</td>
                                <td class="px-4 py-3">{{ $report->engineer?->name }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('service-reports.show', $report) }}" class="text-xs font-semibold text-gray-700 hover:text-gray-900">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $reports->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
