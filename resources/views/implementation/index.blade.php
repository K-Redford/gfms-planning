<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Implementation Capture') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('implementation.index') }}" class="mb-4 flex flex-wrap items-end gap-3">
                <div class="flex flex-col">
                    <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Plant ID or site name" class="mt-1 w-56 rounded-md border border-gray-300 px-3 py-2 text-sm" />
                </div>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="pending_only" value="1" class="rounded border-gray-300 text-gray-900" {{ request()->boolean('pending_only') ? 'checked' : '' }} />
                    Show pending only
                </label>
                <button class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">Apply</button>
                <a href="{{ route('implementation.index') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-700">Reset</a>
                <a href="{{ route('implementation.export', request()->query()) }}" class="ml-auto inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Export CSV</a>
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
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Implemented By</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach ($plants as $plant)
                                @php
                                    $completion = $plant->implementationCompletion;
                                @endphp
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $plant->plant_id }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $plant->site_name }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        @if ($completion)
                                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">Complete</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-700">Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        {{ $completion?->user?->name ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        {{ $completion?->implemented_at?->format('Y-m-d') ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        @if ($completion)
                                            @if (Auth::user()->role === 'admin')
                                                <form method="POST" action="{{ route('implementation.clear', $plant) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button class="rounded-md border border-gray-300 px-3 py-1 text-xs font-semibold text-gray-700 hover:bg-gray-50">Clear</button>
                                                </form>
                                            @else
                                                <span class="text-xs text-gray-400">—</span>
                                            @endif
                                        @else
                                            <form method="POST" action="{{ route('implementation.store') }}" class="flex items-center gap-2 implementation-form">
                                                @csrf
                                                <input type="hidden" name="plant_id" value="{{ $plant->id }}" />
                                                <input type="hidden" name="srf_action" value="later" />
                                                <input type="date" name="implemented_at" class="rounded-md border border-gray-300 px-2 py-1 text-xs" value="{{ now()->format('Y-m-d') }}" required />
                                                <button class="rounded-md bg-gray-900 px-3 py-1 text-xs font-semibold text-white hover:bg-gray-800">Mark Complete</button>
                                            </form>
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

<div id="srf-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
        <h3 class="text-lg font-semibold text-gray-800">Service Report Form required</h3>
        <p class="mt-2 text-sm text-gray-600">An SRF is required after completing Implementation. Do you want to complete it now?</p>
        <div class="mt-6 flex items-center justify-end gap-3">
            <button type="button" id="srf-later" class="rounded-md border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-700">Later</button>
            <button type="button" id="srf-now" class="rounded-md bg-gray-900 px-3 py-2 text-xs font-semibold text-white">Complete Now</button>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('srf-modal');
    const nowBtn = document.getElementById('srf-now');
    const laterBtn = document.getElementById('srf-later');
    let activeForm = null;

    document.querySelectorAll('.implementation-form').forEach((form) => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            activeForm = form;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    });

    const closeModal = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };

    nowBtn.addEventListener('click', () => {
        if (!activeForm) return;
        activeForm.querySelector('input[name="srf_action"]').value = 'now';
        activeForm.submit();
    });

    laterBtn.addEventListener('click', () => {
        if (!activeForm) return;
        activeForm.querySelector('input[name="srf_action"]').value = 'later';
        activeForm.submit();
    });

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });
</script>
