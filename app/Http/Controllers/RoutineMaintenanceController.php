<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use App\Models\RoutineMaintenance;
use App\Models\RoutineMaintenanceItem;
use App\Models\RoutineTask;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RoutineMaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $year = (int) now()->year;
        $search = $request->input('search');
        $pendingOnly = $request->boolean('pending_only');

        $plants = Plant::query()
            ->select('id', 'plant_id', 'site_name')
            ->with(['routineMaintenances' => function ($query) use ($year) {
                $query->where('routine_year', $year)
                    ->with(['performedBy', 'assetCapture.srfTask']);
            }])
            ->when($search, function ($query, $search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('plant_id', 'like', "%{$search}%")
                        ->orWhere('site_name', 'like', "%{$search}%");
                });
            })
            ->when($pendingOnly, function ($query) use ($year) {
                $query->whereDoesntHave('routineMaintenances', function ($sub) use ($year) {
                    $sub->where('routine_year', $year);
                });
            })
            ->orderBy('plant_id')
            ->paginate(25)
            ->withQueryString();

        return view('routine_maintenance.index', [
            'plants' => $plants,
            'year' => $year,
        ]);
    }

    public function create(Request $request)
    {
        $plants = Plant::query()
            ->select('id', 'plant_id', 'site_name')
            ->orderBy('plant_id')
            ->get();

        $tasks = RoutineTask::query()
            ->where('active', true)
            ->orderBy('display_order')
            ->get();

        $selectedPlantId = $request->input('plant');

        return view('routine_maintenance.create', [
            'plants' => $plants,
            'tasks' => $tasks,
            'selectedPlantId' => $selectedPlantId,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'plant_id' => ['required', 'integer', 'exists:plants,id'],
            'performed_at' => ['required', 'date'],
            'tasks' => ['nullable', 'array'],
            'tasks.*' => ['integer', 'exists:routine_tasks,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $year = Carbon::parse($data['performed_at'])->year;

        $exists = RoutineMaintenance::query()
            ->where('plant_id', $data['plant_id'])
            ->where('routine_year', $year)
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['performed_at' => "Routine maintenance has already been completed for {$year}."])
                ->withInput();
        }

        $tasks = RoutineTask::query()
            ->where('active', true)
            ->orderBy('display_order')
            ->get();

        $checked = collect($data['tasks'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->flip();

        $routine = null;

        DB::transaction(function () use (&$routine, $data, $year, $tasks, $checked) {
            $routine = RoutineMaintenance::create([
                'plant_id' => $data['plant_id'],
                'routine_year' => $year,
                'performed_at' => $data['performed_at'],
                'performed_by' => Auth::id(),
                'asset_capture_status' => 'pending',
                'notes' => $data['notes'] ?? null,
            ]);

            $items = $tasks->map(function ($task) use ($routine, $checked) {
                return new RoutineMaintenanceItem([
                    'routine_maintenance_id' => $routine->id,
                    'routine_task_id' => $task->id,
                    'completed' => $checked->has($task->id),
                ]);
            });

            $routine->items()->saveMany($items);
        });

        return redirect()
            ->route('routine-maintenance.show', $routine)
            ->with('status', 'Routine maintenance saved.');
    }

    public function show(RoutineMaintenance $routineMaintenance)
    {
        $routineMaintenance->load([
            'plant',
            'performedBy',
            'items.task',
            'assetCapture.srfTask',
        ]);

        $totalTasks = $routineMaintenance->items->count();
        $completedTasks = $routineMaintenance->items->where('completed', true)->count();

        return view('routine_maintenance.show', [
            'routine' => $routineMaintenance,
            'totalTasks' => $totalTasks,
            'completedTasks' => $completedTasks,
        ]);
    }
}
