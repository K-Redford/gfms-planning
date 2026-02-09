<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Asset Capture') }}
            </h2>
            <div class="flex items-center gap-3">
                @if ($capture->srfTask && in_array($capture->srfTask->status, ['missing', 'draft'], true))
                    <a href="{{ route('service-reports.create', ['task' => $capture->srfTask->id]) }}" class="rounded-md bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-700">Resume SRF</a>
                @endif
                <a href="{{ route('routine-maintenance.index') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-700">Back to routines</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">Plant</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $capture->plant?->plant_id }} - {{ $capture->plant?->site_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">Capture Date</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $capture->visit_date?->format('Y-m-d') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">Incident Number</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $capture->incident_no }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">UIN</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $capture->uin }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">Point of Contact</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $capture->poc_name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">Contact Phone</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $capture->poc_phone ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">Contact Email</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $capture->user_email ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">ATG</p>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $capture->atg ? 'Yes' : 'No' }}
                            @if ($capture->atg?->atg_type)
                                <span class="text-gray-500">({{ $capture->atg->atg_type }})</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-sm font-semibold uppercase tracking-widest text-gray-500">Local Storage Tanks</h3>
                    <div class="mt-4 space-y-2 text-sm text-gray-700">
                        @forelse ($capture->tanks as $tank)
                            <div class="flex items-center justify-between rounded-md border border-gray-200 px-3 py-2">
                                <div>
                                    <span class="font-semibold">{{ $tank->product_name ?? $tank->product_code ?? '-' }}</span>
                                    @if ($tank->product_code)
                                        <span class="text-gray-500">- Code {{ $tank->product_code }}</span>
                                    @endif
                                </div>
                                <div>{{ $tank->capacity_litres ? $tank->capacity_litres . ' L' : '-' }}</div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No tanks recorded.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-sm font-semibold uppercase tracking-widest text-gray-500">Master Controllers</h3>
                    <div class="mt-4 space-y-3 text-sm text-gray-700">
                        @forelse ($capture->masterControllers as $controller)
                            <div class="rounded-md border border-gray-200 px-3 py-2 space-y-1">
                                <div class="font-semibold">Serial: {{ $controller->serial ?? '-' }}</div>
                                <div>Software: {{ $controller->software_version ?? '-' }}</div>
                                <div>Display: {{ $controller->display_type ?? '-' }} | Keypad: {{ $controller->keypad_type ?? '-' }}</div>
                                <div>Tags: Face {{ $controller->asset_tag_faceplate ?? '-' }}, Keypad {{ $controller->asset_tag_keypad ?? '-' }}, Reader {{ $controller->asset_tag_key_reader ?? '-' }}</div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No master controllers recorded.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-sm font-semibold uppercase tracking-widest text-gray-500">Slave Controllers</h3>
                    <div class="mt-4 space-y-3 text-sm text-gray-700">
                        @forelse ($capture->slaveControllers as $controller)
                            <div class="rounded-md border border-gray-200 px-3 py-2 space-y-1">
                                <div class="font-semibold">Serial: {{ $controller->serial ?? '-' }}</div>
                                <div>Display: {{ $controller->display_type ?? '-' }} | Keypad: {{ $controller->keypad_type ?? '-' }}</div>
                                <div>Tags: Face {{ $controller->asset_tag_faceplate ?? '-' }}, Keypad {{ $controller->asset_tag_keypad ?? '-' }}, Reader {{ $controller->asset_tag_key_reader ?? '-' }}</div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No slave controllers recorded.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-sm font-semibold uppercase tracking-widest text-gray-500">Dispensing Pumps</h3>
                    <div class="mt-4 space-y-2 text-sm text-gray-700">
                        @forelse ($capture->pumps as $pump)
                            <div class="rounded-md border border-gray-200 px-3 py-2">{{ $pump->vendor ?? '-' }}</div>
                        @empty
                            <p class="text-sm text-gray-500">No pumps recorded.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-sm font-semibold uppercase tracking-widest text-gray-500">Notes</h3>
                    <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">{{ $capture->notes ?? '-' }}</p>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-sm font-semibold uppercase tracking-widest text-gray-500">Photos</h3>
                    <div class="mt-4 grid gap-4 sm:grid-cols-3">
                        @forelse ($capture->photos as $photo)
                            <div class="rounded-md border border-gray-200 p-2">
                                <img src="{{ asset('storage/' . $photo->url) }}" alt="{{ $photo->description ?? 'Photo' }}" class="h-40 w-full object-cover" />
                                <p class="mt-2 text-xs text-gray-500">{{ $photo->description ?? '' }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No photos uploaded.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
