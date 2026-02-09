<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Routine Maintenance') }}
            </h2>
            <a href="{{ route('routine-maintenance.create') }}" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">New Routine</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('routine-maintenance.index') }}" class="mb-4 flex flex-wrap items-end gap-3">
                <div class="flex flex-col">
                    <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Plant ID or site name" class="mt-1 w-56 rounded-md border border-gray-300 px-3 py-2 text-sm" />
                </div>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="pending_only" value="1" class="rounded border-gray-300 text-gray-900" {{ request()->boolean('pending_only') ? 'checked' : '' }} />
                    Show pending only
                </label>
                <button class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">Apply</button>
                <a href="{{ route('routine-maintenance.index') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-700">Reset</a>
            </form>

            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('status'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Plant ID</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Site Name</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Routine Status ({{ $year }})</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Routine Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Engineer</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Asset Capture</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach ($plants as $plant)
                                @php
                                    $routine = $plant->routineMaintenances->first();
                                    $srfTask = $routine?->assetCapture?->srfTask;
                                @endphp
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $plant->plant_id }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $plant->site_name }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        @if ($routine)
                                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">Complete</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-700">Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        {{ $routine?->performed_at?->format('Y-m-d') ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        {{ $routine?->performedBy?->name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        @if ($routine)
                                            @if ($routine->asset_capture_status === 'complete')
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">Complete</span>
                                                    @if ($srfTask && in_array($srfTask->status, ['missing', 'draft'], true))
                                                        <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-700">SRF Outstanding</span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-700">Pending</span>
                                            @endif
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        @if ($routine)
                                            <div class="flex items-center gap-3">
                                                <a href="{{ route('routine-maintenance.show', $routine) }}" class="text-xs font-semibold text-gray-700 hover:text-gray-900">View</a>
                                                @if ($routine->asset_capture_status === 'complete' && $routine->assetCapture)
                                                    <a href="{{ route('asset-capture.show', $routine->assetCapture) }}" class="text-xs font-semibold text-gray-700 hover:text-gray-900">Asset Capture</a>
                                                @else
                                                    <a href="{{ route('asset-capture.create', ['routine' => $routine->id, 'plant' => $plant->id]) }}" class="text-xs font-semibold text-gray-700 hover:text-gray-900">Asset Capture</a>
                                                @endif
                                                @if ($srfTask && in_array($srfTask->status, ['missing', 'draft'], true))
                                                    <a href="{{ route('service-reports.create', ['task' => $srfTask->id]) }}" class="text-xs font-semibold text-red-600 hover:text-red-700">Resume SRF</a>
                                                @endif
                                            </div>
                                        @else
                                            <a href="{{ route('routine-maintenance.create', ['plant' => $plant->id]) }}" class="rounded-md bg-gray-900 px-3 py-1 text-xs font-semibold text-white hover:bg-gray-800">Start Routine</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                {{ $plants->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
