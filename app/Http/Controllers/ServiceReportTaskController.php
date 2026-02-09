<?php

namespace App\Http\Controllers;

use App\Models\ServiceReportTask;
use Illuminate\Support\Facades\Auth;

class ServiceReportTaskController extends Controller
{
    public function destroy(ServiceReportTask $serviceReportTask)
    {
        if (!in_array(Auth::user()->role, ['admin', 'manager'], true)) {
            abort(403);
        }

        $serviceReportTask->delete();

        return back()->with('status', 'Outstanding SRF cleared.');
    }
}
