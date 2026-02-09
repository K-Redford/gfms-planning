<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Asset Capture') }}
            </h2>
            <a href="{{ route('routine-maintenance.index') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-700">Back to routines</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('asset-capture.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 space-y-4">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Routine</label>
                                <select name="routine_maintenance_id" id="routine-select" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm" required>
                                    <option value="">Select routine</option>
                                    @foreach ($openRoutines as $open)
                                        <option value="{{ $open->id }}"
                                            data-plant-id="{{ $open->plant_id }}"
                                            data-plant-label="{{ $open->plant?->plant_id }} - {{ $open->plant?->site_name }}"
                                            {{ (string) old('routine_maintenance_id', $routine?->id) === (string) $open->id ? 'selected' : '' }}>
                                            {{ $open->plant?->plant_id }} - {{ $open->plant?->site_name }} ({{ $open->performed_at?->format('Y-m-d') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Plant</label>
                                <input type="hidden" name="plant_id" id="plant-id" value="{{ old('plant_id', $routine?->plant_id ?? $selectedPlantId) }}" />
                                <div id="plant-label" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-700">
                                    @if ($routine?->plant)
                                        {{ $routine->plant->plant_id }} - {{ $routine->plant->site_name }}
                                    @else
                                        Select a routine
                                    @endif
                                </div>
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Capture Date</label>
                                <input type="date" name="capture_date" value="{{ old('capture_date', now()->format('Y-m-d')) }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm" required />
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Incident Number</label>
                                <input type="text" name="incident_number" value="{{ old('incident_number') }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm" required />
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">UIN</label>
                                <input type="text" name="uin" value="{{ old('uin') }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm" required />
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-3">
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Point of Contact</label>
                                <input type="text" name="contact_name" value="{{ old('contact_name') }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Contact Phone</label>
                                <input type="text" name="contact_phone" value="{{ old('contact_phone') }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Contact Email</label>
                                <input type="email" name="contact_email" value="{{ old('contact_email') }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold uppercase tracking-widest text-gray-500">Local Storage Tanks</h3>
                            <button type="button" id="add-tank" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">Add tank</button>
                        </div>
                        <div id="tanks-list" class="space-y-3"></div>
                        <p class="text-xs text-gray-500">Capacity is in litres.</p>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold uppercase tracking-widest text-gray-500">Master AFDS Controllers (max 3)</h3>
                            <button type="button" id="add-master" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">Add master</button>
                        </div>
                        <div id="masters-list" class="space-y-3"></div>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold uppercase tracking-widest text-gray-500">Slave Controllers (max 15)</h3>
                            <button type="button" id="add-slave" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">Add slave</button>
                        </div>
                        <div id="slaves-list" class="space-y-3"></div>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold uppercase tracking-widest text-gray-500">Dispensing Pumps</h3>
                            <button type="button" id="add-pump" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">Add pump</button>
                        </div>
                        <div id="pumps-list" class="space-y-3"></div>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 space-y-4">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">ATG Present</label>
                                <select name="atg_present" id="atg-present" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                                    <option value="0" {{ old('atg_present') == '0' ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('atg_present') == '1' ? 'selected' : '' }}>Yes</option>
                                </select>
                            </div>
                            <div id="atg-type-wrap">
                                <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">ATG Type</label>
                                <select name="atg_type" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                                    <option value="">Select type</option>
                                    @foreach ($atgTypes as $type)
                                        <option value="{{ $type }}" {{ old('atg_type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Notes</label>
                            <textarea name="notes" rows="3" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">{{ old('notes') }}</textarea>
                        </div>

                        <div>
                            <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Photo Uploads</label>
                            <input type="file" name="photos[]" multiple class="mt-1 w-full text-sm text-gray-700" />
                            <p class="mt-2 text-xs text-gray-500">Upload any relevant site photos.</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('routine-maintenance.index') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-700">Cancel</a>
                    <button class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">Save Asset Capture</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

<template id="tank-row-template">
    <div class="grid gap-3 sm:grid-cols-3 rounded-md border border-gray-100 px-3 py-2">
        <div>
            <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Product</label>
            <select class="mt-1 w-full rounded-md border border-gray-300 px-2 py-1 text-sm">
                <option value="">Select product</option>
                @foreach ($productOptions as $option)
                    <option value="{{ $option['name'] }}" data-code="{{ $option['code'] }}">{{ $option['name'] }}</option>
                @endforeach
            </select>
            <input type="hidden" class="product-code" />
        </div>
        <div>
            <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Capacity (L)</label>
            <input type="number" min="0" class="mt-1 w-full rounded-md border border-gray-300 px-2 py-1 text-sm" />
        </div>
        <div class="flex items-end justify-end">
            <button type="button" class="remove-row text-xs font-semibold text-red-600 hover:text-red-700">Remove</button>
        </div>
    </div>
</template>

<template id="master-row-template">
    <div class="rounded-md border border-gray-100 px-3 py-3">
        <div class="grid gap-3 sm:grid-cols-3">
            <div>
                <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Serial Number</label>
                <input type="text" class="mt-1 w-full rounded-md border border-gray-300 px-2 py-1 text-sm" />
            </div>
            <div>
                <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Software Version</label>
                <input type="text" class="mt-1 w-full rounded-md border border-gray-300 px-2 py-1 text-sm" />
            </div>
            <div>
                <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Display Type</label>
                <select class="mt-1 w-full rounded-md border border-gray-300 px-2 py-1 text-sm">
                    <option value="">Select</option>
                    <option value="Large">Large</option>
                    <option value="Small">Small</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Keypad Type</label>
                <select class="mt-1 w-full rounded-md border border-gray-300 px-2 py-1 text-sm">
                    <option value="">Select</option>
                    <option value="Metal">Metal</option>
                    <option value="Composite">Composite</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Asset Tag Face Plate</label>
                <input type="text" class="mt-1 w-full rounded-md border border-gray-300 px-2 py-1 text-sm" />
            </div>
            <div>
                <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Asset Tag Key Pad</label>
                <input type="text" class="mt-1 w-full rounded-md border border-gray-300 px-2 py-1 text-sm" />
            </div>
            <div>
                <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Asset Tag Key Reader</label>
                <input type="text" class="mt-1 w-full rounded-md border border-gray-300 px-2 py-1 text-sm" />
            </div>
        </div>
        <div class="mt-2 flex justify-end">
            <button type="button" class="remove-row text-xs font-semibold text-red-600 hover:text-red-700">Remove</button>
        </div>
    </div>
</template>

<template id="slave-row-template">
    <div class="rounded-md border border-gray-100 px-3 py-3">
        <div class="grid gap-3 sm:grid-cols-3">
            <div>
                <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Serial Number</label>
                <input type="text" class="mt-1 w-full rounded-md border border-gray-300 px-2 py-1 text-sm" />
            </div>
            <div>
                <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Display Type</label>
                <select class="mt-1 w-full rounded-md border border-gray-300 px-2 py-1 text-sm">
                    <option value="">Select</option>
                    <option value="Large">Large</option>
                    <option value="Small">Small</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Keypad Type</label>
                <select class="mt-1 w-full rounded-md border border-gray-300 px-2 py-1 text-sm">
                    <option value="">Select</option>
                    <option value="Metal">Metal</option>
                    <option value="Composite">Composite</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Asset Tag Face Plate</label>
                <input type="text" class="mt-1 w-full rounded-md border border-gray-300 px-2 py-1 text-sm" />
            </div>
            <div>
                <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Asset Tag Key Pad</label>
                <input type="text" class="mt-1 w-full rounded-md border border-gray-300 px-2 py-1 text-sm" />
            </div>
            <div>
                <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Asset Tag Key Reader</label>
                <input type="text" class="mt-1 w-full rounded-md border border-gray-300 px-2 py-1 text-sm" />
            </div>
        </div>
        <div class="mt-2 flex justify-end">
            <button type="button" class="remove-row text-xs font-semibold text-red-600 hover:text-red-700">Remove</button>
        </div>
    </div>
</template>

<template id="pump-row-template">
    <div class="grid gap-3 sm:grid-cols-3 rounded-md border border-gray-100 px-3 py-2">
        <div class="sm:col-span-2">
            <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Pump Vendor</label>
            <select class="mt-1 w-full rounded-md border border-gray-300 px-2 py-1 text-sm">
                <option value="">Select vendor</option>
                @foreach ($pumpVendors as $vendor)
                    <option value="{{ $vendor }}">{{ $vendor }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end justify-end">
            <button type="button" class="remove-row text-xs font-semibold text-red-600 hover:text-red-700">Remove</button>
        </div>
    </div>
</template>

<script>
    const addRow = (list, templateId, maxRows, callback) => {
        if (maxRows && list.children.length >= maxRows) return;
        const template = document.getElementById(templateId);
        const clone = template.content.firstElementChild.cloneNode(true);
        if (callback) callback(clone, list.children.length);
        list.appendChild(clone);
        clone.querySelectorAll('.remove-row').forEach((btn) => {
            btn.addEventListener('click', () => clone.remove());
        });
    };

    const tankList = document.getElementById('tanks-list');
    const addTankBtn = document.getElementById('add-tank');

    const bindTankRow = (row, index) => {
        const productSelect = row.querySelector('select');
        const productCodeInput = row.querySelector('.product-code');
        const capacityInput = row.querySelector('input[type="number"]');

        productSelect.name = `tanks[${index}][product_name]`;
        productCodeInput.name = `tanks[${index}][product_code]`;
        capacityInput.name = `tanks[${index}][capacity_litres]`;

        productSelect.addEventListener('change', () => {
            const code = productSelect.selectedOptions[0]?.dataset?.code || '';
            productCodeInput.value = code;
        });
    };

    addTankBtn.addEventListener('click', () => {
        addRow(tankList, 'tank-row-template', 10, bindTankRow);
    });

    addRow(tankList, 'tank-row-template', 10, bindTankRow);
    addRow(tankList, 'tank-row-template', 10, bindTankRow);

    const masterList = document.getElementById('masters-list');
    const addMasterBtn = document.getElementById('add-master');

    const bindMasterRow = (row, index) => {
        const inputs = row.querySelectorAll('input, select');
        inputs[0].name = `masters[${index}][serial_number]`;
        inputs[1].name = `masters[${index}][software_version]`;
        inputs[2].name = `masters[${index}][display_type]`;
        inputs[3].name = `masters[${index}][keypad_type]`;
        inputs[4].name = `masters[${index}][asset_tag_faceplate]`;
        inputs[5].name = `masters[${index}][asset_tag_keypad]`;
        inputs[6].name = `masters[${index}][asset_tag_key_reader]`;
    };

    addMasterBtn.addEventListener('click', () => {
        addRow(masterList, 'master-row-template', 3, bindMasterRow);
    });

    const slaveList = document.getElementById('slaves-list');
    const addSlaveBtn = document.getElementById('add-slave');

    const bindSlaveRow = (row, index) => {
        const inputs = row.querySelectorAll('input, select');
        inputs[0].name = `slaves[${index}][serial_number]`;
        inputs[1].name = `slaves[${index}][display_type]`;
        inputs[2].name = `slaves[${index}][keypad_type]`;
        inputs[3].name = `slaves[${index}][asset_tag_faceplate]`;
        inputs[4].name = `slaves[${index}][asset_tag_keypad]`;
        inputs[5].name = `slaves[${index}][asset_tag_key_reader]`;
    };

    addSlaveBtn.addEventListener('click', () => {
        addRow(slaveList, 'slave-row-template', 15, bindSlaveRow);
    });

    const pumpList = document.getElementById('pumps-list');
    const addPumpBtn = document.getElementById('add-pump');

    const bindPumpRow = (row, index) => {
        const select = row.querySelector('select');
        select.name = `pumps[${index}][vendor]`;
    };

    addPumpBtn.addEventListener('click', () => {
        addRow(pumpList, 'pump-row-template', 15, bindPumpRow);
    });

    const atgPresent = document.getElementById('atg-present');
    const atgTypeWrap = document.getElementById('atg-type-wrap');

    const toggleAtg = () => {
        atgTypeWrap.style.display = atgPresent.value === '1' ? 'block' : 'none';
    };

    atgPresent.addEventListener('change', toggleAtg);
    toggleAtg();

    const routineSelect = document.getElementById('routine-select');
    const plantIdInput = document.getElementById('plant-id');
    const plantLabel = document.getElementById('plant-label');

    const updatePlantFromRoutine = () => {
        const option = routineSelect.selectedOptions[0];
        const plantId = option?.dataset?.plantId || '';
        const label = option?.dataset?.plantLabel || 'Select a routine';
        plantIdInput.value = plantId;
        plantLabel.textContent = label;
    };

    routineSelect.addEventListener('change', updatePlantFromRoutine);
    updatePlantFromRoutine();
</script>
