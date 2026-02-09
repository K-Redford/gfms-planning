<?php

namespace App\Mail;

use App\Models\ServiceReport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ServiceReportSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ServiceReport $report)
    {
    }

    public function build(): self
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('service_reports.pdf', [
            'report' => $this->report,
        ]);

        return $this->subject('Service Report Form Submitted')
            ->view('emails.service_report_submitted', [
                'report' => $this->report,
            ])
            ->attachData($pdf->output(), 'SRF-' . $this->report->serial_number . '.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
