<?php

namespace App\Http\Controllers;

use App\Models\AssetCapture;
use App\Models\AssetCaptureAtg;
use App\Models\AssetCaptureMasterController;
use App\Models\AssetCapturePhoto;
use App\Models\AssetCapturePump;
use App\Models\AssetCaptureSlaveController;
use App\Models\AssetCaptureTank;
use App\Models\Plant;
use App\Models\RoutineMaintenance;
use App\Models\ServiceReportTask;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class AssetCaptureController extends Controller
{
    public function create(Request $request)
    {
        $routine = null;
        if ($request->filled('routine')) {
            $routine = RoutineMaintenance::query()
                ->with('plant')
                ->find($request->input('routine'));
        }

        $openRoutines = RoutineMaintenance::query()
            ->with('plant')
            ->where('asset_capture_status', 'pending')
            ->orderByDesc('performed_at')
            ->limit(50)
            ->get();

        return view('asset_capture.create', [
            'selectedPlantId' => $request->input('plant'),
            'routine' => $routine,
            'openRoutines' => $openRoutines,
            'productOptions' => $this->productOptions(),
            'atgTypes' => $this->atgTypes(),
            'pumpVendors' => $this->pumpVendors(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'routine_maintenance_id' => ['required', 'integer', 'exists:routine_maintenances,id'],
            'plant_id' => ['required', 'integer', 'exists:plants,id'],
            'capture_date' => ['required', 'date'],
            'incident_number' => ['required', 'string', 'max:100'],
            'uin' => ['required', 'string', 'max:50'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'atg_present' => ['nullable', 'boolean'],
            'atg_type' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
            'tanks' => ['nullable', 'array'],
            'tanks.*.product_name' => ['nullable', 'string', 'max:100'],
            'tanks.*.product_code' => ['nullable', 'integer'],
            'tanks.*.capacity_litres' => ['nullable', 'integer', 'min:0'],
            'masters' => ['nullable', 'array'],
            'masters.*.serial_number' => ['nullable', 'string', 'max:100'],
            'masters.*.software_version' => ['nullable', 'string', 'max:100'],
            'masters.*.display_type' => ['nullable', 'string', 'max:20'],
            'masters.*.keypad_type' => ['nullable', 'string', 'max:20'],
            'masters.*.asset_tag_faceplate' => ['nullable', 'string', 'max:100'],
            'masters.*.asset_tag_keypad' => ['nullable', 'string', 'max:100'],
            'masters.*.asset_tag_key_reader' => ['nullable', 'string', 'max:100'],
            'slaves' => ['nullable', 'array'],
            'slaves.*.serial_number' => ['nullable', 'string', 'max:100'],
            'slaves.*.display_type' => ['nullable', 'string', 'max:20'],
            'slaves.*.keypad_type' => ['nullable', 'string', 'max:20'],
            'slaves.*.asset_tag_faceplate' => ['nullable', 'string', 'max:100'],
            'slaves.*.asset_tag_keypad' => ['nullable', 'string', 'max:100'],
            'slaves.*.asset_tag_key_reader' => ['nullable', 'string', 'max:100'],
            'pumps' => ['nullable', 'array'],
            'pumps.*.vendor' => ['nullable', 'string', 'max:50'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['file', 'image', 'max:5120'],
        ]);

        $routine = RoutineMaintenance::query()
            ->with('assetCapture')
            ->findOrFail($data['routine_maintenance_id']);

        if ($routine->assetCapture) {
            return back()
                ->withErrors(['routine_maintenance_id' => 'Asset capture already exists for this routine.'])
                ->withInput();
        }

        $atgPresent = (bool) ($data['atg_present'] ?? false);

        $capture = null;
        $task = null;

        DB::transaction(function () use (&$capture, $data, $routine, $atgPresent, $request) {
            $capture = AssetCapture::create([
                'routine_maintenance_id' => $routine->id,
                'plant_id' => $routine->plant_id,
                'performed_by' => $routine->performed_by ?? auth()->id(),
                'performed_at' => now(),
                'visit_date' => $data['capture_date'],
                'incident_no' => $data['incident_number'],
                'service_sheet_no' => null,
                'flc' => null,
                'uin' => $data['uin'],
                'poc_name' => $data['contact_name'] ?? null,
                'poc_phone' => $data['contact_phone'] ?? null,
                'user_email' => $data['contact_email'] ?? null,
                'unit_address' => null,
                'postcode' => null,
                'country' => null,
                'notes' => $data['notes'] ?? null,
                'source_form_id' => null,
                'source_form_email' => null,
                'source_form_name' => null,
                'created_at' => now(),
            ]);

            $tanks = collect($data['tanks'] ?? [])
                ->filter(fn ($tank) => !empty($tank['product_name']) || !empty($tank['capacity_litres']))
                ->values()
                ->map(function ($tank, $index) use ($capture) {
                    return new AssetCaptureTank([
                        'capture_id' => $capture->id,
                        'tank_number' => $index + 1,
                        'product_code' => $tank['product_code'] ?? null,
                        'product_name' => $tank['product_name'] ?? null,
                        'capacity_litres' => $tank['capacity_litres'] ?? null,
                        'notes' => null,
                    ]);
                });

            if ($tanks->isNotEmpty()) {
                $capture->tanks()->saveMany($tanks);
            }

            if ($atgPresent) {
                AssetCaptureAtg::create([
                    'capture_id' => $capture->id,
                    'atg_type' => $data['atg_type'] ?? null,
                    'notes' => null,
                ]);
            }

            $masters = collect($data['masters'] ?? [])
                ->filter(fn ($master) => collect($master)->filter()->isNotEmpty())
                ->map(function ($master) use ($capture) {
                    return new AssetCaptureMasterController([
                        'capture_id' => $capture->id,
                        'serial' => $master['serial_number'] ?? null,
                        'software_version' => $master['software_version'] ?? null,
                        'comm_method' => null,
                        'display_type' => $master['display_type'] ?? null,
                        'keypad_type' => $master['keypad_type'] ?? null,
                        'asset_tag_faceplate' => $master['asset_tag_faceplate'] ?? null,
                        'asset_tag_keypad' => $master['asset_tag_keypad'] ?? null,
                        'asset_tag_key_reader' => $master['asset_tag_key_reader'] ?? null,
                        'qty' => 1,
                    ]);
                });

            if ($masters->isNotEmpty()) {
                $capture->masterControllers()->saveMany($masters);
            }

            $slaves = collect($data['slaves'] ?? [])
                ->filter(fn ($slave) => collect($slave)->filter()->isNotEmpty())
                ->map(function ($slave) use ($capture) {
                    return new AssetCaptureSlaveController([
                        'capture_id' => $capture->id,
                        'serial' => $slave['serial_number'] ?? null,
                        'display_type' => $slave['display_type'] ?? null,
                        'keypad_type' => $slave['keypad_type'] ?? null,
                        'asset_tag_faceplate' => $slave['asset_tag_faceplate'] ?? null,
                        'asset_tag_keypad' => $slave['asset_tag_keypad'] ?? null,
                        'asset_tag_key_reader' => $slave['asset_tag_key_reader'] ?? null,
                        'qty' => 1,
                    ]);
                });

            if ($slaves->isNotEmpty()) {
                $capture->slaveControllers()->saveMany($slaves);
            }

            $pumps = collect($data['pumps'] ?? [])
                ->filter(fn ($pump) => !empty($pump['vendor']))
                ->map(function ($pump) use ($capture) {
                    return new AssetCapturePump([
                        'capture_id' => $capture->id,
                        'vendor' => $pump['vendor'] ?? null,
                        'model' => null,
                        'qty' => 1,
                    ]);
                });

            if ($pumps->isNotEmpty()) {
                $capture->pumps()->saveMany($pumps);
            }

            if ($request->hasFile('photos')) {
                $photos = [];
                foreach ($request->file('photos') as $photo) {
                    if (!$photo->isValid()) {
                        continue;
                    }
                    $path = $photo->store('asset-captures', 'public');
                    $photos[] = new AssetCapturePhoto([
                        'capture_id' => $capture->id,
                        'url' => $path,
                        'description' => $photo->getClientOriginalName(),
                    ]);
                }
                if (!empty($photos)) {
                    $capture->photos()->saveMany($photos);
                }
            }

            $routine->update(['asset_capture_status' => 'complete']);
        });

        $task = ServiceReportTask::query()
            ->where('asset_capture_id', $capture->id)
            ->first();

        if (!$task) {
            $task = ServiceReportTask::create([
                'asset_capture_id' => $capture->id,
                'plant_id' => $routine->plant_id,
                'engineer_user_id' => auth()->id(),
                'status' => 'draft',
            ]);
        }

        return redirect()
            ->route('service-reports.create', ['task' => $task->id])
            ->with('status', 'Asset capture saved. Please complete the Service Report Form.');
    }

    public function show(AssetCapture $assetCapture)
    {
        $assetCapture->load([
            'plant',
            'routine',
            'tanks',
            'atg',
            'masterControllers',
            'slaveControllers',
            'pumps',
            'photos',
            'srfTask',
        ]);

        return view('asset_capture.show', [
            'capture' => $assetCapture,
        ]);
    }

    private function productOptions(): array
    {
        return [
            ['name' => 'ULGAS', 'code' => 1],
            ['name' => 'DIESO F76', 'code' => 2],
            ['name' => 'DIESO MIL', 'code' => 3],
            ['name' => 'MTGAS', 'code' => 4],
            ['name' => 'DIESO MT', 'code' => 5],
            ['name' => 'DIESO UK', 'code' => 6],
            ['name' => 'ADBLUE', 'code' => 7],
        ];
    }

    private function atgTypes(): array
    {
        return [
            'VR TSL350R',
            'VR TLS4',
            'VR TLS450(Inc Plus)',
            'Other',
        ];
    }

    private function pumpVendors(): array
    {
        return [
            'Pumptronics',
            'Saltzkotten',
            'Hytek',
            'Other',
        ];
    }
}
