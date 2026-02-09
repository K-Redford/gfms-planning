<?php

namespace App\Http\Controllers;

use App\Models\ImplementationCompletion;
use App\Models\Plant;
use App\Models\ServiceReportTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImplementationController extends Controller
{
    public function index()
    {
        $plants = $this->buildQuery()
            ->paginate(25)
            ->withQueryString();

        return view('implementation.index', [
            'plants' => $plants,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'plant_id' => ['required', 'integer', 'exists:plants,id'],
            'implemented_at' => ['required', 'date'],
            'srf_action' => ['nullable', 'string', 'in:now,later'],
        ]);

        $existing = ImplementationCompletion::query()
            ->where('plant_id', $data['plant_id'])
            ->first();

        if ($existing) {
            return back()->withErrors([
                'implemented_at' => 'Implementation has already been marked complete for this site.',
            ]);
        }

        $completion = ImplementationCompletion::create([
            'plant_id' => $data['plant_id'],
            'implemented_at' => $data['implemented_at'],
            'implemented_by' => Auth::id(),
        ]);

        $action = $data['srf_action'] ?? 'later';

        $task = ServiceReportTask::create([
            'implementation_completion_id' => $completion->id,
            'plant_id' => $completion->plant_id,
            'engineer_user_id' => Auth::id(),
            'status' => $action === 'now' ? 'draft' : 'missing',
        ]);

        if ($action === 'now') {
            return redirect()
                ->route('service-reports.create', ['task' => $task->id])
                ->with('status', 'Please complete the Service Report Form.');
        }

        return back()->with('status', 'Implementation marked complete. SRF marked as outstanding.');
    }

    public function clear(Plant $plant)
    {
        ImplementationCompletion::query()
            ->where('plant_id', $plant->id)
            ->delete();

        return back()->with('status', 'Implementation cleared.');
    }

    public function export(Request $request): StreamedResponse
    {
        $plants = $this->buildQuery()->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="implementation-export.csv"',
        ];

        return response()->streamDownload(function () use ($plants) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Plant ID', 'Site Name', 'Status', 'Implemented By', 'Implemented Date']);

            foreach ($plants as $plant) {
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
        }, 'implementation-export.csv', $headers);
    }

    private function buildQuery()
    {
        $search = request('search');
        $pendingOnly = request()->boolean('pending_only');

        return Plant::query()
            ->select('id', 'plant_id', 'site_name')
            ->with(['implementationCompletion.user'])
            ->when($search, function ($query, $search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('plant_id', 'like', "%{$search}%")
                        ->orWhere('site_name', 'like', "%{$search}%");
                });
            })
            ->when($pendingOnly, function ($query) {
                $query->whereDoesntHave('implementationCompletion');
            })
            ->orderBy('plant_id');
    }
}
