<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Service Report Form') }}
        </h2>
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

            <form method="POST" action="{{ route('service-reports.store') }}" class="space-y-6" id="srf-form">
                @csrf
                @if ($task)
                    <input type="hidden" name="task_id" value="{{ $task->id }}" />
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
                            <p class="text-2xl font-semibold text-gray-800">{{ $nextSerial ?? 'Auto' }}</p>
                        </div>
                    </div>

                    <h3 class="mt-4 text-center text-2xl font-semibold text-gray-800">Service Report Form</h3>

                    <div class="mt-6 grid gap-4 md:grid-cols-3">
                        <div class="md:col-span-2">
                            <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Company</label>
                            <input type="text" name="company_name" id="company_name" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm" value="{{ old('company_name', $task?->plant?->site_name) }}" required />
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">GFMS Plant ID</label>
                            <select name="plant_id" id="plant_id" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm" required>
                                <option value="">Select Plant</option>
                                @foreach ($plants as $plant)
                                    <option value="{{ $plant->id }}" data-site-name="{{ $plant->site_name }}" {{ (string) old('plant_id', $task?->plant_id) === (string) $plant->id ? 'selected' : '' }}>{{ $plant->plant_id }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Site Address</label>
                        <input type="text" name="site_address" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
                    </div>

                    <div class="mt-4 grid gap-4 md:grid-cols-4">
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Date of Visit</label>
                            <input type="date" name="date_of_visit" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm" value="{{ old('date_of_visit', $task?->completion?->implemented_at?->format('Y-m-d') ?? $task?->assetCapture?->visit_date?->format('Y-m-d')) }}" required />
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Time on Site</label>
                            <input type="time" name="time_on_site" id="time_on_site" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Time off Site</label>
                            <input type="time" name="time_off_site" id="time_off_site" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Site Time</label>
                            <input type="text" id="site_time_display" class="mt-1 w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm" readonly />
                        </div>
                    </div>

                    <div class="mt-4 grid gap-4 md:grid-cols-4">
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Mileage</label>
                            <input type="number" name="mileage" min="0" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Travel Time (hrs)</label>
                            <select name="travel_time_hours" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                                <option value="">Select</option>
                                @for ($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Order No.</label>
                            <input type="text" name="order_number" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">TransFlo Ref</label>
                            <input type="text" name="transflo_ref" maxlength="10" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
                        </div>
                    </div>

                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Charge Type</label>
                            <select name="charge_type" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm" required>
                                <option value="">Select</option>
                                @foreach (['Charge', 'No Charge', 'Contract', 'Warranty', 'Installation', 'Routine', 'Implementation'] as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Equipment Type</label>
                            <select name="equipment_type" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm" required>
                                <option value="">Select</option>
                                @foreach (['GFMS', 'AFDS', 'Pump Interface', 'Software', 'Routine'] as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Reported Fault</label>
                        <input type="text" name="reported_fault" maxlength="255" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
                    </div>

                    <div class="mt-4">
                        <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Report Details</label>
                        <textarea name="report_details" rows="5" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm"></textarea>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="grid gap-6 md:grid-cols-3">
                        <div>
                            <h4 class="text-sm font-semibold uppercase tracking-widest text-gray-500">Departure Status</h4>
                            <div class="mt-3 space-y-2 text-sm">
                                @foreach (['Operational/Conclusive', 'Operational/Inconclusive', 'Removal to Workshop', 'Insufficient Spares', 'Software Problem', 'Requires further visit'] as $status)
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="departure_statuses[]" value="{{ $status }}" class="rounded border-gray-300 text-gray-900" />
                                        {{ $status }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold uppercase tracking-widest text-gray-500">Software Changes</h4>
                            <input type="text" name="software_changes" class="mt-3 w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold uppercase tracking-widest text-gray-500">Engineer Signature</h4>
                            <div class="mt-3">
                                <canvas class="w-full rounded-md border border-gray-300" height="160" data-signature="engineer"></canvas>
                                <input type="hidden" name="engineer_signature" data-signature-input="engineer" />
                                <button type="button" class="mt-2 text-xs font-semibold text-gray-500" data-signature-clear="engineer">Clear</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h4 class="text-sm font-semibold uppercase tracking-widest text-gray-500">Parts Supplied</h4>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full text-sm" id="parts-table">
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
                                @for ($i = 0; $i < 3; $i++)
                                    <tr>
                                        <td class="px-2 py-2">
                                            <select name="parts[{{ $i }}][part_description]" class="w-full rounded-md border border-gray-300 px-2 py-1 text-sm part-description">
                                                <option value="">Select Part</option>
                                                @foreach ($spares as $spare)
                                                    <option value="{{ $spare->part_description }}" data-stock-code="{{ $spare->sage_id }}">{{ $spare->part_description }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-2 py-2">
                                            <input type="text" name="parts[{{ $i }}][stock_code]" class="w-full rounded-md border border-gray-300 px-2 py-1 text-sm stock-code" />
                                        </td>
                                        <td class="px-2 py-2">
                                            <input type="number" min="0" name="parts[{{ $i }}][quantity]" class="w-full rounded-md border border-gray-300 px-2 py-1 text-sm quantity" />
                                        </td>
                                        <td class="px-2 py-2">
                                            <input type="number" min="0" step="0.01" name="parts[{{ $i }}][price_each]" class="w-full rounded-md border border-gray-300 px-2 py-1 text-sm price-each" />
                                        </td>
                                        <td class="px-2 py-2">
                                            <input type="number" min="0" step="0.01" name="parts[{{ $i }}][total_price]" class="w-full rounded-md border border-gray-300 px-2 py-1 text-sm total-price" />
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                    <button type="button" id="add-part-row" class="mt-3 text-xs font-semibold text-gray-500">Add row</button>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <h4 class="text-sm font-semibold uppercase tracking-widest text-gray-500">Customer Signature</h4>
                            <div class="mt-3">
                                <canvas class="w-full rounded-md border border-gray-300" height="160" data-signature="customer"></canvas>
                                <input type="hidden" name="customer_signature" data-signature-input="customer" />
                                <button type="button" class="mt-2 text-xs font-semibold text-gray-500" data-signature-clear="customer">Clear</button>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Print Name</label>
                                <input type="text" name="customer_print_name" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Rank/Civ</label>
                                <input type="text" name="customer_rank_civ" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Customer Email</label>
                                <input type="email" name="customer_email" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Notes</label>
                        <textarea name="notes" rows="3" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm"></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('service-reports.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700">Cancel</a>
                    <button class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">Submit SRF</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const plantSelect = document.getElementById('plant_id');
        const companyInput = document.getElementById('company_name');
        plantSelect.addEventListener('change', () => {
            const selected = plantSelect.options[plantSelect.selectedIndex];
            if (selected && selected.dataset.siteName) {
                companyInput.value = selected.dataset.siteName;
            }
        });

        const onTimeInput = document.getElementById('time_on_site');
        const offTimeInput = document.getElementById('time_off_site');
        const siteTimeDisplay = document.getElementById('site_time_display');
        const updateSiteTime = () => {
            if (!onTimeInput.value || !offTimeInput.value) {
                siteTimeDisplay.value = '';
                return;
            }
            const [onH, onM] = onTimeInput.value.split(':').map(Number);
            const [offH, offM] = offTimeInput.value.split(':').map(Number);
            let start = onH * 60 + onM;
            let end = offH * 60 + offM;
            if (end < start) end += 24 * 60;
            const diff = end - start;
            const hours = Math.floor(diff / 60);
            const minutes = diff % 60;
            siteTimeDisplay.value = `${hours}h ${minutes}m`;
        };
        onTimeInput.addEventListener('input', updateSiteTime);
        offTimeInput.addEventListener('input', updateSiteTime);

        const table = document.getElementById('parts-table').querySelector('tbody');
        const addRowButton = document.getElementById('add-part-row');
        addRowButton.addEventListener('click', () => {
            const index = table.children.length;
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-2 py-2">
                    <select name="parts[${index}][part_description]" class="w-full rounded-md border border-gray-300 px-2 py-1 text-sm part-description">
                        <option value="">Select Part</option>
                        ${Array.from(document.querySelectorAll('.part-description option')).map(o => `<option value="${o.value}" data-stock-code="${o.dataset.stockCode || ''}">${o.textContent}</option>`).join('')}
                    </select>
                </td>
                <td class="px-2 py-2">
                    <input type="text" name="parts[${index}][stock_code]" class="w-full rounded-md border border-gray-300 px-2 py-1 text-sm stock-code" />
                </td>
                <td class="px-2 py-2">
                    <input type="number" min="0" name="parts[${index}][quantity]" class="w-full rounded-md border border-gray-300 px-2 py-1 text-sm quantity" />
                </td>
                <td class="px-2 py-2">
                    <input type="number" min="0" step="0.01" name="parts[${index}][price_each]" class="w-full rounded-md border border-gray-300 px-2 py-1 text-sm price-each" />
                </td>
                <td class="px-2 py-2">
                    <input type="number" min="0" step="0.01" name="parts[${index}][total_price]" class="w-full rounded-md border border-gray-300 px-2 py-1 text-sm total-price" />
                </td>
            `;
            table.appendChild(row);
        });

        const updatePartRow = (row) => {
            const qty = parseFloat(row.querySelector('.quantity')?.value || '0');
            const price = parseFloat(row.querySelector('.price-each')?.value || '0');
            const total = row.querySelector('.total-price');
            if (total) total.value = (qty * price).toFixed(2);
        };

        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('part-description')) {
                const stock = e.target.selectedOptions[0]?.dataset.stockCode || '';
                const row = e.target.closest('tr');
                row.querySelector('.stock-code').value = stock;
            }
        });

        document.addEventListener('input', (e) => {
            if (e.target.classList.contains('quantity') || e.target.classList.contains('price-each')) {
                updatePartRow(e.target.closest('tr'));
            }
        });

        const setupSignature = (key) => {
            const canvas = document.querySelector(`canvas[data-signature="${key}"]`);
            const input = document.querySelector(`input[data-signature-input="${key}"]`);
            const clearButton = document.querySelector(`button[data-signature-clear="${key}"]`);
            const ctx = canvas.getContext('2d');
            const resizeCanvas = () => {
                const rect = canvas.getBoundingClientRect();
                const scale = window.devicePixelRatio || 1;
                canvas.width = rect.width * scale;
                canvas.height = rect.height * scale;
                ctx.setTransform(scale, 0, 0, scale, 0, 0);
            };
            resizeCanvas();
            window.addEventListener('resize', resizeCanvas);
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            let drawing = false;
            const getPos = (e) => {
                const rect = canvas.getBoundingClientRect();
                const touch = e.touches?.[0];
                const clientX = touch ? touch.clientX : e.clientX;
                const clientY = touch ? touch.clientY : e.clientY;
                return {
                    x: clientX - rect.left,
                    y: clientY - rect.top,
                };
            };
            const start = (e) => {
                drawing = true;
                const pos = getPos(e);
                ctx.beginPath();
                ctx.moveTo(pos.x, pos.y);
            };
            const draw = (e) => {
                if (!drawing) return;
                const pos = getPos(e);
                ctx.lineTo(pos.x, pos.y);
                ctx.stroke();
            };
            const end = () => {
                if (!drawing) return;
                drawing = false;
                input.value = canvas.toDataURL('image/png');
            };
            canvas.addEventListener('mousedown', start);
            canvas.addEventListener('mousemove', draw);
            canvas.addEventListener('mouseup', end);
            canvas.addEventListener('mouseleave', end);
            canvas.addEventListener('touchstart', (e) => { e.preventDefault(); start(e); }, { passive: false });
            canvas.addEventListener('touchmove', (e) => { e.preventDefault(); draw(e); }, { passive: false });
            canvas.addEventListener('touchend', end);
            clearButton.addEventListener('click', () => {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                input.value = '';
            });
        };
        setupSignature('engineer');
        setupSignature('customer');
    </script>
</x-app-layout>
