<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-3">
                <a href="{{ route('implementation.index') }}" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">GFMS Implementation</p>
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M7 4h10a2 2 0 0 1 2 2v14H5V6a2 2 0 0 1 2-2z" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-2 text-sm text-slate-700">Capture implementation progress.</p>
                </a>
                <a href="{{ route('routine-maintenance.index') }}" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">GFMS Routines</p>
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 1 1-20 0 10 10 0 0 1 20 0Z" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-2 text-sm text-slate-700">Complete annual routine tasks.</p>
                </a>
                <a href="{{ route('service-reports.create') }}" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">GFMS Incidents</p>
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M4.93 4.93l14.14 14.14M7.1 7.1a7 7 0 1 1 9.9 9.9" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-2 text-sm text-slate-700">Start a new Service Report Form.</p>
                </a>
            </div>

            @if (in_array(Auth::user()->role, ['admin', 'manager'], true))
                <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-sm font-semibold uppercase tracking-widest text-gray-500">Implementation Progress</h3>
                        <div class="mt-4 flex items-center justify-between text-sm text-gray-600">
                            <span>{{ $completedPlants }} of {{ $totalPlants }} sites complete</span>
                            <span class="font-semibold text-gray-800">{{ $completionPercent }}%</span>
                        </div>
                        <div class="mt-3 h-3 w-full rounded-full bg-gray-200">
                            <div class="h-3 rounded-full bg-gray-900" style="width: {{ $completionPercent }}%"></div>
                        </div>
                    </div>
                </div>
            @endif

            @if (in_array(Auth::user()->role, ['admin', 'manager'], true))
                <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-sm font-semibold uppercase tracking-widest text-gray-500">Routine Maintenance</h3>
                        <p class="mt-1 text-sm text-gray-600">Recent annual routines and asset capture status.</p>
                        <div class="mt-4 space-y-2 text-sm text-gray-700">
                            @forelse ($recentRoutines as $routine)
                                <div class="flex items-center justify-between rounded-md border border-gray-200 px-3 py-2">
                                    <div>
                                        <span class="font-semibold">{{ $routine->plant?->plant_id }}</span>
                                        <span class="text-gray-500">-</span>
                                        <span>{{ $routine->plant?->site_name }}</span>
                                        <span class="text-gray-500">-</span>
                                        <span>{{ $routine->performed_at?->format('Y-m-d') }}</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        @if ($routine->asset_capture_status === 'complete')
                                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">Asset Capture Complete</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-700">Asset Capture Pending</span>
                                        @endif
                                        <a href="{{ route('routine-maintenance.show', $routine) }}" class="text-xs font-semibold text-gray-700 hover:text-gray-900">View</a>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">No routine maintenance records yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif

            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold uppercase tracking-widest text-slate-500">Service Report Forms</h3>
                        <p class="mt-1 text-sm text-slate-600">Create and review completed SRFs.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if (in_array(Auth::user()->role, ['admin', 'manager'], true))
                            <a href="{{ route('service-reports.index') }}" class="rounded-md border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">View All</a>
                            <a href="{{ route('service-reports.index', ['mine' => 1]) }}" class="rounded-md border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">View My SRFs</a>
                        @else
                            <a href="{{ route('service-reports.index', ['mine' => 1]) }}" class="rounded-md border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">View My SRFs</a>
                        @endif
                        <a href="{{ route('service-reports.create') }}" class="rounded-md bg-gray-900 px-3 py-2 text-xs font-semibold text-white hover:bg-gray-800">New SRF</a>
                    </div>
                </div>
            </div>

            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold uppercase tracking-widest text-gray-500">Outstanding SRFs</h3>
                        <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-700">{{ $outstandingSrfCount }}</span>
                    </div>
                    <p class="mt-1 text-sm text-gray-600">Implementation completed but SRF not submitted.</p>
                    <div class="mt-4 space-y-2 text-sm text-gray-700">
                        @forelse ($outstandingSrfTasks as $task)
                                <div class="flex items-center justify-between rounded-md border border-gray-200 px-3 py-2">
                                    <div>
                                        <span class="font-semibold">{{ $task->plant?->plant_id }}</span>
                                        <span class="text-gray-500">·</span>
                                        <span>{{ $task->plant?->site_name }}</span>
                                        <span class="text-gray-500">·</span>
                                        <span>{{ $task->engineer?->name }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('service-reports.create', ['task' => $task->id]) }}" class="rounded-md bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-700">Complete SRF</a>
                                        @if (in_array(Auth::user()->role, ['admin', 'manager'], true))
                                            <form method="POST" action="{{ route('service-report-tasks.destroy', $task) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">Dismiss</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                        @empty
                            <p class="text-sm text-gray-500">No outstanding SRFs.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            @if (in_array(Auth::user()->role, ['admin', 'manager'], true))
                <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-sm font-semibold uppercase tracking-widest text-gray-500">All SRF Status</h3>
                        <p class="mt-1 text-sm text-gray-600">Latest SRFs tracked through the system.</p>
                        <div class="mt-4 space-y-2 text-sm text-gray-700">
                            @forelse ($recentSrfTasks as $task)
                                <div class="flex items-center justify-between rounded-md border border-gray-200 px-3 py-2">
                                    <div>
                                        <span class="font-semibold">{{ $task->plant?->plant_id }}</span>
                                        <span class="text-gray-500">·</span>
                                        <span>{{ $task->plant?->site_name }}</span>
                                        <span class="text-gray-500">·</span>
                                        <span>{{ $task->engineer?->name }}</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs font-semibold uppercase tracking-widest text-gray-500">{{ $task->status }}</span>
                                        @if ($task->report)
                                            <a href="{{ route('service-reports.show', $task->report) }}" class="text-xs font-semibold text-gray-700 hover:text-gray-900">View</a>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">No SRFs yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @else
                <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-sm font-semibold uppercase tracking-widest text-gray-500">My Submitted SRFs</h3>
                        <p class="mt-1 text-sm text-gray-600">Your recently completed SRFs.</p>
                        <div class="mt-4 space-y-2 text-sm text-gray-700">
                            @forelse ($engineerRecentSrfs as $report)
                                <div class="flex items-center justify-between rounded-md border border-gray-200 px-3 py-2">
                                    <div>
                                        <span class="font-semibold">{{ $report->plant?->plant_id }}</span>
                                        <span class="text-gray-500">·</span>
                                        <span>{{ $report->company_name }}</span>
                                        <span class="text-gray-500">·</span>
                                        <span>{{ $report->date_of_visit?->format('Y-m-d') }}</span>
                                    </div>
                                    <a href="{{ route('service-reports.show', $report) }}" class="text-xs font-semibold text-gray-700 hover:text-gray-900">View</a>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">No submitted SRFs yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
