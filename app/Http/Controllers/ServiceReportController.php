<?php

namespace App\Http\Controllers;

use App\Mail\ServiceReportSubmitted;
use App\Models\Plant;
use App\Models\ServiceReport;
use App\Models\ServiceReportPart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceReportController extends Controller
{
    public function index()
    {
        $reports = ServiceReport::query()
            ->with(['plant', 'engineer'])
            ->when(request()->boolean('mine'), function ($query) {
                $query->where('engineer_user_id', Auth::id());
            })
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('service_reports.index', [
            'reports' => $reports,
        ]);
    }

    public function create()
    {
        $task = null;
        if (request('task')) {
            $task = \App\Models\ServiceReportTask::query()
                ->with(['plant', 'completion', 'assetCapture'])
                ->find(request('task'));
        }

        $plants = Plant::query()
            ->select('id', 'plant_id', 'site_name')
            ->orderBy('plant_id')
            ->get();

        $spares = \DB::table('authorized_spares')
            ->select('part_description', 'sage_id')
            ->orderBy('part_description')
            ->get();

        return view('service_reports.create', [
            'plants' => $plants,
            'spares' => $spares,
            'nextSerial' => $this->nextSerialNumber(),
            'task' => $task,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'task_id' => ['nullable', 'integer', 'exists:service_report_tasks,id'],
            'plant_id' => ['required', 'integer', 'exists:plants,id'],
            'company_name' => ['required', 'string', 'max:255'],
            'site_address' => ['nullable', 'string', 'max:255'],
            'date_of_visit' => ['required', 'date'],
            'time_on_site' => ['nullable', 'date_format:H:i'],
            'time_off_site' => ['nullable', 'date_format:H:i'],
            'travel_time_hours' => ['nullable', 'integer', 'min:1', 'max:10'],
            'mileage' => ['nullable', 'integer', 'min:0'],
            'order_number' => ['nullable', 'string', 'max:50'],
            'transflo_ref' => ['nullable', 'string', 'max:10'],
            'charge_type' => ['required', 'string', 'max:30'],
            'equipment_type' => ['required', 'string', 'max:30'],
            'reported_fault' => ['nullable', 'string', 'max:255'],
            'report_details' => ['nullable', 'string'],
            'departure_statuses' => ['nullable', 'array'],
            'departure_statuses.*' => ['string', 'max:50'],
            'software_changes' => ['nullable', 'string', 'max:255'],
            'engineer_signature' => ['nullable', 'string'],
            'customer_signature' => ['nullable', 'string'],
            'customer_print_name' => ['nullable', 'string', 'max:255'],
            'customer_rank_civ' => ['nullable', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'notes' => ['nullable', 'string'],
            'parts' => ['nullable', 'array'],
            'parts.*.part_description' => ['nullable', 'string', 'max:255'],
            'parts.*.stock_code' => ['nullable', 'string', 'max:50'],
            'parts.*.quantity' => ['nullable', 'integer', 'min:0'],
            'parts.*.price_each' => ['nullable', 'numeric', 'min:0'],
            'parts.*.total_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $siteTimeMinutes = $this->calculateSiteTimeMinutes(
            $data['time_on_site'] ?? null,
            $data['time_off_site'] ?? null
        );

        $report = ServiceReport::create([
            'serial_number' => $this->nextSerialNumber(),
            'plant_id' => $data['plant_id'],
            'company_name' => $data['company_name'],
            'site_address' => $data['site_address'] ?? null,
            'date_of_visit' => $data['date_of_visit'],
            'time_on_site' => $data['time_on_site'] ?? null,
            'time_off_site' => $data['time_off_site'] ?? null,
            'site_time_minutes' => $siteTimeMinutes,
            'travel_time_hours' => $data['travel_time_hours'] ?? null,
            'mileage' => $data['mileage'] ?? null,
            'order_number' => $data['order_number'] ?? null,
            'transflo_ref' => $data['transflo_ref'] ?? null,
            'charge_type' => $data['charge_type'],
            'equipment_type' => $data['equipment_type'],
            'reported_fault' => $data['reported_fault'] ?? null,
            'report_details' => $data['report_details'] ?? null,
            'departure_statuses' => $data['departure_statuses'] ?? [],
            'software_changes' => $data['software_changes'] ?? null,
            'engineer_signature_path' => $this->storeSignature($data['engineer_signature'] ?? null, 'engineer'),
            'customer_signature_path' => $this->storeSignature($data['customer_signature'] ?? null, 'customer'),
            'customer_print_name' => $data['customer_print_name'] ?? null,
            'customer_rank_civ' => $data['customer_rank_civ'] ?? null,
            'customer_email' => $data['customer_email'] ?? null,
            'notes' => $data['notes'] ?? null,
            'engineer_user_id' => Auth::id(),
        ]);

        $parts = collect($data['parts'] ?? [])
            ->filter(function ($part) {
                return !empty($part['part_description']) || !empty($part['stock_code']) || !empty($part['quantity']);
            })
            ->map(function ($part) use ($report) {
                return new ServiceReportPart([
                    'service_report_id' => $report->id,
                    'part_description' => $part['part_description'] ?? null,
                    'stock_code' => $part['stock_code'] ?? null,
                    'quantity' => $part['quantity'] ?? null,
                    'price_each' => $part['price_each'] ?? null,
                    'total_price' => $part['total_price'] ?? null,
                ]);
            });

        if ($parts->isNotEmpty()) {
            $report->parts()->saveMany($parts);
        }

        if (!empty($data['task_id'])) {
            \App\Models\ServiceReportTask::query()
                ->where('id', $data['task_id'])
                ->update([
                    'service_report_id' => $report->id,
                    'status' => 'submitted',
                ]);
        }

        $this->sendServiceReportEmail($report);

        return redirect()
            ->route('service-reports.show', $report)
            ->with('status', 'Service report submitted.');
    }

    public function show(ServiceReport $serviceReport)
    {
        $serviceReport->load(['plant', 'engineer', 'parts']);

        return view('service_reports.show', [
            'report' => $serviceReport,
        ]);
    }

    private function calculateSiteTimeMinutes(?string $timeOn, ?string $timeOff): ?int
    {
        if (!$timeOn || !$timeOff) {
            return null;
        }

        $start = \Carbon\Carbon::createFromFormat('H:i', $timeOn);
        $end = \Carbon\Carbon::createFromFormat('H:i', $timeOff);

        if ($end->lessThan($start)) {
            $end->addDay();
        }

        return $start->diffInMinutes($end);
    }

    private function storeSignature(?string $dataUrl, string $prefix): ?string
    {
        if (!$dataUrl) {
            return null;
        }

        if (!str_starts_with($dataUrl, 'data:image/')) {
            return null;
        }

        [$meta, $content] = explode(',', $dataUrl, 2);
        $extension = str_contains($meta, 'image/png') ? 'png' : 'jpg';
        $filename = "service-reports/{$prefix}-" . Str::uuid() . ".{$extension}";

        Storage::disk('public')->put($filename, base64_decode($content));

        return $filename;
    }

    private function nextSerialNumber(): int
    {
        $latest = ServiceReport::query()->max('serial_number');

        return $latest ? $latest + 1 : 40000;
    }

    private function sendServiceReportEmail(ServiceReport $report): void
    {
        $testRecipient = env('SRF_TEST_TO');

        if ($testRecipient) {
            Mail::to($testRecipient)->send(new ServiceReportSubmitted($report));
            return;
        }

        $recipients = array_filter([
            $report->customer_email,
            $report->engineer?->email,
        ]);

        if (!empty($recipients)) {
            Mail::to($recipients)->send(new ServiceReportSubmitted($report));
        }
    }
}
