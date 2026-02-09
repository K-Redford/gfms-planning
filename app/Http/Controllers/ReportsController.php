<?php

namespace App\Http\Controllers;

use App\Models\ImplementationCompletion;
use App\Models\AssetCapture;
use App\Models\AssetCaptureTank;
use App\Models\Plant;
use App\Models\RoutineMaintenance;
use App\Models\ServiceReport;
use App\Models\ServiceReportTask;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportsController extends Controller
{
    public function index()
    {
        $this->authorizeReportAccess();

        return view('reports.index');
    }

    public function internal()
    {
        return $this->renderReport('internal');
    }

    public function internalCsv(): StreamedResponse
    {
        return $this->exportCsv('internal');
    }

    public function internalPdf()
    {
        return $this->renderReport('internal', true);
    }

    public function bduk()
    {
        return $this->renderReport('bduk');
    }

    public function bdukCsv(): StreamedResponse
    {
        return $this->exportCsv('bduk');
    }

    public function bdukPdf()
    {
        return $this->renderReport('bduk', true);
    }

    public function mod()
    {
        return $this->renderReport('mod');
    }

    public function modCsv(): StreamedResponse
    {
        return $this->exportCsv('mod');
    }

    public function modPdf()
    {
        return $this->renderReport('mod', true);
    }

    public function dsfaTanks(Request $request)
    {
        return $this->renderDsfaTanks($request);
    }

    public function dsfaTanksPdf(Request $request)
    {
        return $this->renderDsfaTanks($request, true);
    }

    public function routineStatus(Request $request)
    {
        return $this->renderRoutineStatus($request);
    }

    public function routineStatusPdf(Request $request)
    {
        return $this->renderRoutineStatus($request, true);
    }

    public function outstandingSrfs(Request $request)
    {
        return $this->renderOutstandingSrfs($request);
    }

    public function outstandingSrfsPdf(Request $request)
    {
        return $this->renderOutstandingSrfs($request, true);
    }

    public function assetCaptureCompliance(Request $request)
    {
        return $this->renderAssetCaptureCompliance($request);
    }

    public function assetCaptureCompliancePdf(Request $request)
    {
        return $this->renderAssetCaptureCompliance($request, true);
    }

    public function srfVolume(Request $request)
    {
        return $this->renderSrfVolume($request);
    }

    public function srfVolumePdf(Request $request)
    {
        return $this->renderSrfVolume($request, true);
    }

    private function renderDsfaTanks(Request $request, bool $print = false)
    {
        $this->authorizeReportAccess();

        $plantFilter = $request->input('plant_id');

        $plantIds = Plant::query()
            ->when($plantFilter, function ($query) use ($plantFilter) {
                $query->where('plant_id', 'like', "%{$plantFilter}%");
            })
            ->pluck('id')
            ->all();

        $latestSub = DB::table('asset_captures')
            ->select('plant_id', DB::raw('MAX(visit_date) as latest_visit'))
            ->when($plantFilter, function ($query) use ($plantIds) {
                if (!empty($plantIds)) {
                    $query->whereIn('plant_id', $plantIds);
                } else {
                    $query->whereRaw('1 = 0');
                }
            })
            ->groupBy('plant_id');

        $captures = AssetCapture::query()
            ->from('asset_captures as ac')
            ->select('ac.id', 'ac.plant_id', 'ac.visit_date')
            ->joinSub($latestSub, 'latest', function ($join) {
                $join->on('ac.plant_id', '=', 'latest.plant_id')
                    ->on('ac.visit_date', '=', 'latest.latest_visit');
            })
            ->orderBy('ac.plant_id')
            ->get();

        $plants = Plant::query()
            ->select('id', 'plant_id', 'site_name')
            ->when($plantFilter, function ($query) use ($plantFilter) {
                $query->where('plant_id', 'like', "%{$plantFilter}%");
            })
            ->get()
            ->keyBy('id');

        $plantOptions = Plant::query()
            ->select('plant_id', 'site_name')
            ->orderBy('plant_id')
            ->get();

        $captureIds = $captures->pluck('id')->all();
        $tanks = AssetCaptureTank::query()
            ->whereIn('capture_id', $captureIds)
            ->orderBy('tank_number')
            ->get()
            ->groupBy('capture_id');

        $rows = $captures->map(function ($capture) use ($plants, $tanks) {
            $plant = $plants->get($capture->plant_id);
            return [
                'plant_id' => $plant?->plant_id ?? '',
                'site_name' => $plant?->site_name ?? '',
                'visit_date' => $capture->visit_date,
                'tanks' => $tanks->get($capture->id, collect()),
            ];
        });

        return view('reports.dsfa_tanks', [
            'rows' => $rows,
            'plantFilter' => $plantFilter,
            'plantOptions' => $plantOptions,
            'generatedAt' => now(),
            'print' => $print,
        ]);
    }

    private function renderRoutineStatus(Request $request, bool $print = false)
    {
        $this->authorizeReportAccess();

        $year = (int) ($request->input('year') ?? now()->year);
        $plantFilter = $request->input('plant_id');

        $plantsQuery = Plant::query()
            ->select('id', 'plant_id', 'site_name')
            ->orderBy('plant_id');

        if ($plantFilter) {
            $plantsQuery->where('plant_id', $plantFilter);
        }

        $plants = $plantsQuery->get();

        $routines = RoutineMaintenance::query()
            ->with(['assetCapture', 'performedBy'])
            ->where('routine_year', $year)
            ->when($plantFilter, function ($query) use ($plants) {
                $query->whereIn('plant_id', $plants->pluck('id'));
            })
            ->get()
            ->keyBy('plant_id');

        $assetCaptureIds = $routines
            ->pluck('assetCapture')
            ->filter()
            ->pluck('id')
            ->all();

        $tasks = ServiceReportTask::query()
            ->whereIn('asset_capture_id', $assetCaptureIds)
            ->get()
            ->keyBy('asset_capture_id');

        $rows = $plants->map(function ($plant) use ($routines, $tasks) {
            $routine = $routines->get($plant->id);
            $assetCapture = $routine?->assetCapture;
            $task = $assetCapture ? $tasks->get($assetCapture->id) : null;

            return [
                'plant' => $plant,
                'routine' => $routine,
                'asset_capture' => $assetCapture,
                'srf_status' => $task?->status ?? null,
            ];
        });

        $years = RoutineMaintenance::query()
            ->select('routine_year')
            ->distinct()
            ->orderByDesc('routine_year')
            ->pluck('routine_year')
            ->all();
        if (empty($years)) {
            $years = [now()->year];
        }

        return view('reports.routine_status', [
            'rows' => $rows,
            'year' => $year,
            'years' => $years,
            'plantFilter' => $plantFilter,
            'plantOptions' => Plant::query()->select('plant_id', 'site_name')->orderBy('plant_id')->get(),
            'generatedAt' => now(),
            'print' => $print,
        ]);
    }

    private function renderOutstandingSrfs(Request $request, bool $print = false)
    {
        $this->authorizeReportAccess();

        $statusFilter = $request->input('status');
        $plantFilter = $request->input('plant_id');
        $engineerFilter = $request->input('engineer_user_id');
        $minDays = $request->input('min_days');

        $tasksQuery = ServiceReportTask::query()
            ->with(['plant', 'engineer'])
            ->whereIn('status', ['missing', 'draft'])
            ->orderByDesc('created_at');

        if ($statusFilter && in_array($statusFilter, ['missing', 'draft'], true)) {
            $tasksQuery->where('status', $statusFilter);
        }

        if ($plantFilter) {
            $plantIds = Plant::query()
                ->where('plant_id', $plantFilter)
                ->pluck('id');
            $tasksQuery->whereIn('plant_id', $plantIds);
        }

        if ($engineerFilter) {
            $tasksQuery->where('engineer_user_id', $engineerFilter);
        }

        $tasks = $tasksQuery->get()->map(function ($task) {
            $task->age_days = $task->created_at?->diffInDays(now()) ?? null;
            return $task;
        });

        if (is_numeric($minDays)) {
            $tasks = $tasks->filter(fn ($task) => $task->age_days !== null && $task->age_days >= (int) $minDays)->values();
        }

        return view('reports.outstanding_srfs', [
            'tasks' => $tasks,
            'statusFilter' => $statusFilter,
            'plantFilter' => $plantFilter,
            'engineerFilter' => $engineerFilter,
            'minDays' => $minDays,
            'plantOptions' => Plant::query()->select('plant_id', 'site_name')->orderBy('plant_id')->get(),
            'engineerOptions' => User::query()->select('id', 'name')->orderBy('name')->get(),
            'generatedAt' => now(),
            'print' => $print,
        ]);
    }

    private function renderAssetCaptureCompliance(Request $request, bool $print = false)
    {
        $this->authorizeReportAccess();

        $year = (int) ($request->input('year') ?? now()->year);
        $plantFilter = $request->input('plant_id');

        $query = RoutineMaintenance::query()
            ->with(['plant', 'performedBy'])
            ->where('routine_year', $year)
            ->where('asset_capture_status', 'pending')
            ->orderBy('performed_at');

        if ($plantFilter) {
            $plantIds = Plant::query()
                ->where('plant_id', $plantFilter)
                ->pluck('id');
            $query->whereIn('plant_id', $plantIds);
        }

        $rows = $query->get();

        $years = RoutineMaintenance::query()
            ->select('routine_year')
            ->distinct()
            ->orderByDesc('routine_year')
            ->pluck('routine_year')
            ->all();
        if (empty($years)) {
            $years = [now()->year];
        }

        return view('reports.asset_capture_compliance', [
            'rows' => $rows,
            'year' => $year,
            'years' => $years,
            'plantFilter' => $plantFilter,
            'plantOptions' => Plant::query()->select('plant_id', 'site_name')->orderBy('plant_id')->get(),
            'generatedAt' => now(),
            'print' => $print,
        ]);
    }

    private function renderSrfVolume(Request $request, bool $print = false)
    {
        $this->authorizeReportAccess();

        $year = (int) ($request->input('year') ?? now()->year);
        $engineerFilter = $request->input('engineer_user_id');
        $chargeFilter = $request->input('charge_type');

        $query = ServiceReport::query()
            ->with('engineer')
            ->whereYear('date_of_visit', $year);

        if ($engineerFilter) {
            $query->where('engineer_user_id', $engineerFilter);
        }

        if ($chargeFilter) {
            $query->where('charge_type', $chargeFilter);
        }

        $reports = $query->get();

        $months = collect(range(1, 12))->mapWithKeys(function ($month) {
            return [$month => \Carbon\Carbon::create()->month($month)->format('M')];
        });

        $grouped = $reports->groupBy('engineer_user_id')->map(function ($items) use ($months) {
            $counts = $months->keys()->mapWithKeys(function ($month) use ($items) {
                return [$month => $items->filter(fn ($report) => $report->date_of_visit?->month === $month)->count()];
            });
            return [
                'engineer' => $items->first()->engineer,
                'counts' => $counts,
                'total' => $items->count(),
            ];
        })->values();

        $years = ServiceReport::query()
            ->selectRaw('YEAR(date_of_visit) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->all();
        if (empty($years)) {
            $years = [now()->year];
        }

        $chargeTypes = ServiceReport::query()
            ->select('charge_type')
            ->distinct()
            ->orderBy('charge_type')
            ->pluck('charge_type')
            ->all();

        return view('reports.srf_volume', [
            'rows' => $grouped,
            'months' => $months,
            'year' => $year,
            'years' => $years,
            'engineerFilter' => $engineerFilter,
            'engineerOptions' => User::query()->select('id', 'name')->orderBy('name')->get(),
            'chargeFilter' => $chargeFilter,
            'chargeOptions' => $chargeTypes,
            'generatedAt' => now(),
            'print' => $print,
        ]);
    }

    private function renderReport(string $audience, bool $print = false)
    {
        $this->authorizeReportAccess();

        $data = $this->buildReportData();

        return view('reports.show', array_merge($data, [
            'audience' => $audience,
            'print' => $print,
        ]));
    }

    private function exportCsv(string $audience): StreamedResponse
    {
        $this->authorizeReportAccess();

        $data = $this->buildReportData();

        $filename = "implementation-report-{$audience}.csv";

        return response()->streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Plant ID', 'Site Name', 'Status', 'Implemented By', 'Implemented Date']);

            foreach ($data['plants'] as $plant) {
                $completion = $plant->implementationCompletion;
                fputcsv($handle, [
                    $plant->plant_id,
                    $plant->site_name,
                    $completion ? 'Complete' : 'Pending',
                    $completion?->user?->name ?? '',
                    $completion?->implemented_at?->format('Y-m-d') ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function buildReportData(): array
    {
        $plants = Plant::query()
            ->select('id', 'plant_id', 'site_name')
            ->with(['implementationCompletion.user'])
            ->orderBy('plant_id')
            ->get();

        $totalPlants = $plants->count();
        $completedPlants = $plants->filter(fn ($plant) => (bool) $plant->implementationCompletion)->count();
        $pendingPlants = $totalPlants - $completedPlants;
        $completionPercent = $totalPlants > 0
            ? (int) round(($completedPlants / $totalPlants) * 100)
            : 0;

        return [
            'plants' => $plants,
            'totalPlants' => $totalPlants,
            'completedPlants' => $completedPlants,
            'pendingPlants' => $pendingPlants,
            'completionPercent' => $completionPercent,
            'generatedAt' => now(),
        ];
    }

    private function authorizeReportAccess(): void
    {
        if (!in_array(Auth::user()->role, ['admin', 'manager'], true)) {
            abort(403);
        }
    }
}
