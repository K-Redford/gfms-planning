<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $totalPlants = \App\Models\Plant::query()->count();
    $completedPlants = \App\Models\ImplementationCompletion::query()->count();
    $completionPercent = $totalPlants > 0
        ? (int) round(($completedPlants / $totalPlants) * 100)
        : 0;
    $outstandingQuery = \App\Models\ServiceReportTask::query()
        ->with(['plant', 'engineer'])
        ->whereIn('status', ['missing', 'draft'])
        ->orderByDesc('created_at');

    if (!in_array(\Illuminate\Support\Facades\Auth::user()->role, ['admin', 'manager'], true)) {
        $outstandingQuery->where('engineer_user_id', \Illuminate\Support\Facades\Auth::id());
    }

    $outstandingSrfTasks = $outstandingQuery->limit(10)->get();

    $recentSrfTasks = \App\Models\ServiceReportTask::query()
        ->with(['plant', 'engineer', 'report'])
        ->orderByDesc('created_at')
        ->limit(10)
        ->get();

    $outstandingSrfCount = \App\Models\ServiceReportTask::query()
        ->whereIn('status', ['missing', 'draft'])
        ->when(!in_array(\Illuminate\Support\Facades\Auth::user()->role, ['admin', 'manager'], true), function ($query) {
            $query->where('engineer_user_id', \Illuminate\Support\Facades\Auth::id());
        })
        ->count();

    $engineerRecentSrfs = \App\Models\ServiceReport::query()
        ->with('plant')
        ->where('engineer_user_id', \Illuminate\Support\Facades\Auth::id())
        ->orderByDesc('created_at')
        ->limit(10)
        ->get();

    $recentRoutines = \App\Models\RoutineMaintenance::query()
        ->with(['plant', 'performedBy'])
        ->orderByDesc('performed_at')
        ->limit(10)
        ->get();

    return view('dashboard', [
        'totalPlants' => $totalPlants,
        'completedPlants' => $completedPlants,
        'completionPercent' => $completionPercent,
        'outstandingSrfTasks' => $outstandingSrfTasks,
        'recentSrfTasks' => $recentSrfTasks,
        'engineerRecentSrfs' => $engineerRecentSrfs,
        'recentRoutines' => $recentRoutines,
        'outstandingSrfCount' => $outstandingSrfCount,
    ]);
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/implementation', [\App\Http\Controllers\ImplementationController::class, 'index'])->name('implementation.index');
    Route::get('/implementation/export', [\App\Http\Controllers\ImplementationController::class, 'export'])->name('implementation.export');
    Route::post('/implementation', [\App\Http\Controllers\ImplementationController::class, 'store'])->name('implementation.store');

    Route::get('/routine-maintenance', [\App\Http\Controllers\RoutineMaintenanceController::class, 'index'])->name('routine-maintenance.index');
    Route::get('/routine-maintenance/create', [\App\Http\Controllers\RoutineMaintenanceController::class, 'create'])->name('routine-maintenance.create');
    Route::post('/routine-maintenance', [\App\Http\Controllers\RoutineMaintenanceController::class, 'store'])->name('routine-maintenance.store');
    Route::get('/routine-maintenance/{routineMaintenance}', [\App\Http\Controllers\RoutineMaintenanceController::class, 'show'])->name('routine-maintenance.show');

    Route::get('/asset-capture/create', [\App\Http\Controllers\AssetCaptureController::class, 'create'])->name('asset-capture.create');
    Route::post('/asset-capture', [\App\Http\Controllers\AssetCaptureController::class, 'store'])->name('asset-capture.store');
    Route::get('/asset-capture/{assetCapture}', [\App\Http\Controllers\AssetCaptureController::class, 'show'])->name('asset-capture.show');

    Route::get('/service-reports', [\App\Http\Controllers\ServiceReportController::class, 'index'])->name('service-reports.index');
    Route::get('/service-reports/create', [\App\Http\Controllers\ServiceReportController::class, 'create'])->name('service-reports.create');
    Route::post('/service-reports', [\App\Http\Controllers\ServiceReportController::class, 'store'])->name('service-reports.store');
    Route::get('/service-reports/{serviceReport}', [\App\Http\Controllers\ServiceReportController::class, 'show'])->name('service-reports.show');
    Route::delete('/service-report-tasks/{serviceReportTask}', [\App\Http\Controllers\ServiceReportTaskController::class, 'destroy'])->name('service-report-tasks.destroy');

    Route::get('/reports', [\App\Http\Controllers\ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/internal', [\App\Http\Controllers\ReportsController::class, 'internal'])->name('reports.internal');
    Route::get('/reports/internal.csv', [\App\Http\Controllers\ReportsController::class, 'internalCsv'])->name('reports.internal.csv');
    Route::get('/reports/internal.pdf', [\App\Http\Controllers\ReportsController::class, 'internalPdf'])->name('reports.internal.pdf');
    Route::get('/reports/bduk', [\App\Http\Controllers\ReportsController::class, 'bduk'])->name('reports.bduk');
    Route::get('/reports/bduk.csv', [\App\Http\Controllers\ReportsController::class, 'bdukCsv'])->name('reports.bduk.csv');
    Route::get('/reports/bduk.pdf', [\App\Http\Controllers\ReportsController::class, 'bdukPdf'])->name('reports.bduk.pdf');
    Route::get('/reports/mod', [\App\Http\Controllers\ReportsController::class, 'mod'])->name('reports.mod');
    Route::get('/reports/mod.csv', [\App\Http\Controllers\ReportsController::class, 'modCsv'])->name('reports.mod.csv');
    Route::get('/reports/mod.pdf', [\App\Http\Controllers\ReportsController::class, 'modPdf'])->name('reports.mod.pdf');
    Route::get('/reports/dsfa-tanks', [\App\Http\Controllers\ReportsController::class, 'dsfaTanks'])->name('reports.dsfa-tanks');
    Route::get('/reports/dsfa-tanks.pdf', [\App\Http\Controllers\ReportsController::class, 'dsfaTanksPdf'])->name('reports.dsfa-tanks.pdf');
    Route::get('/reports/routine-status', [\App\Http\Controllers\ReportsController::class, 'routineStatus'])->name('reports.routine-status');
    Route::get('/reports/routine-status.pdf', [\App\Http\Controllers\ReportsController::class, 'routineStatusPdf'])->name('reports.routine-status.pdf');
    Route::get('/reports/outstanding-srfs', [\App\Http\Controllers\ReportsController::class, 'outstandingSrfs'])->name('reports.outstanding-srfs');
    Route::get('/reports/outstanding-srfs.pdf', [\App\Http\Controllers\ReportsController::class, 'outstandingSrfsPdf'])->name('reports.outstanding-srfs.pdf');
    Route::get('/reports/asset-capture-compliance', [\App\Http\Controllers\ReportsController::class, 'assetCaptureCompliance'])->name('reports.asset-capture-compliance');
    Route::get('/reports/asset-capture-compliance.pdf', [\App\Http\Controllers\ReportsController::class, 'assetCaptureCompliancePdf'])->name('reports.asset-capture-compliance.pdf');
    Route::get('/reports/srf-volume', [\App\Http\Controllers\ReportsController::class, 'srfVolume'])->name('reports.srf-volume');
    Route::get('/reports/srf-volume.pdf', [\App\Http\Controllers\ReportsController::class, 'srfVolumePdf'])->name('reports.srf-volume.pdf');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::patch('/implementation/{plant}/clear', [\App\Http\Controllers\ImplementationController::class, 'clear'])->name('implementation.clear');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
    Route::post('/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/toggle-active', [\App\Http\Controllers\Admin\UserController::class, 'toggleActive'])->name('users.toggle-active');
});

require __DIR__.'/auth.php';
