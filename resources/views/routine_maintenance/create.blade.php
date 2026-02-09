<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Routine Maintenance') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($errors->any())
                        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('routine-maintenance.store') }}" class="space-y-6">
                        @csrf

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Plant</label>
                                <select name="plant_id" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm" required>
                                    <option value="">Select plant</option>
                                    @foreach ($plants as $plant)
                                        <option value="{{ $plant->id }}"
                                            {{ (string) old('plant_id', $selectedPlantId) === (string) $plant->id ? 'selected' : '' }}>
                                            {{ $plant->plant_id }} - {{ $plant->site_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Routine Date</label>
                                <input type="date" name="performed_at" value="{{ old('performed_at', now()->format('Y-m-d')) }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm" required />
                            </div>
                        </div>

                        <div>
                            <label class="text-xs font-semibold uppercase tracking-widest text-gray-500">Notes</label>
                            <textarea name="notes" rows="3" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="Optional notes">{{ old('notes') }}</textarea>
                        </div>

                        <div>
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-semibold uppercase tracking-widest text-gray-500">Routine Tasks</h3>
                                <p class="text-xs text-gray-500">Asset capture must be completed alongside this routine.</p>
                            </div>
                            <div class="mt-4 space-y-3">
                                @foreach ($tasks as $task)
                                    <label class="flex items-start gap-3 rounded-md border border-gray-100 px-3 py-2 hover:border-gray-200">
                                        <input type="checkbox" name="tasks[]" value="{{ $task->id }}" class="mt-1 rounded border-gray-300 text-gray-900"
                                            {{ in_array($task->id, old('tasks', [])) ? 'checked' : '' }} />
                                        <span class="text-sm text-gray-700">
                                            <span class="font-semibold text-gray-600">{{ $task->display_order }}.</span>
                                            {{ $task->task_text }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('routine-maintenance.index') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-700">Cancel</a>
                            <button class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">Save Routine</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
