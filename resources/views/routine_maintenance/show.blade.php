<x-app-layout>
    <x-slot name="header">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Routine Maintenance') }}
                </h2>
            <div class="flex items-center gap-3">
                @if ($routine->asset_capture_status === 'complete' && $routine->assetCapture)
                    <a href="{{ route('asset-capture.show', $routine->assetCapture) }}" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">Asset Capture</a>
                @else
                    <a href="{{ route('asset-capture.create', ['routine' => $routine->id, 'plant' => $routine->plant_id]) }}" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">Asset Capture</a>
                @endif
                @if ($routine->assetCapture?->srfTask && in_array($routine->assetCapture->srfTask->status, ['missing', 'draft'], true))
                    <a href="{{ route('service-reports.create', ['task' => $routine->assetCapture->srfTask->id]) }}" class="rounded-md bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-700">Resume SRF</a>
                @endif
                <a href="{{ route('routine-maintenance.index') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-700">Back to list</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 space-y-6">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">Plant</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $routine->plant?->plant_id }} - {{ $routine->plant?->site_name }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">Routine Date</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $routine->performed_at?->format('Y-m-d') }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">Engineer</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $routine->performedBy?->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">Asset Capture</p>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $routine->asset_capture_status === 'complete' ? 'Complete' : 'Pending' }}
                            </p>
                        </div>
                    </div>

                    <div class="rounded-md border border-gray-200 px-4 py-3 text-sm text-gray-700">
                        Completed {{ $completedTasks }} of {{ $totalTasks }} tasks.
                    </div>

                    @if ($routine->notes)
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">Notes</p>
                            <p class="mt-1 text-sm text-gray-700 whitespace-pre-line">{{ $routine->notes }}</p>
                        </div>
                    @endif

                    <div>
                        <h3 class="text-sm font-semibold uppercase tracking-widest text-gray-500">Task Checklist</h3>
                        <div class="mt-4 space-y-3">
                            @foreach ($routine->items->sortBy(fn ($item) => $item->task?->display_order) as $item)
                                <div class="flex items-start gap-3 rounded-md border border-gray-100 px-3 py-2">
                                    <span class="mt-0.5 text-xs font-semibold {{ $item->completed ? 'text-emerald-700' : 'text-amber-700' }}">
                                        {{ $item->completed ? 'DONE' : 'TODO' }}
                                    </span>
                                    <span class="text-sm text-gray-700">
                                        <span class="font-semibold text-gray-600">{{ $item->task?->display_order }}.</span>
                                        {{ $item->task?->task_text }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
