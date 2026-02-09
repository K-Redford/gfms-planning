<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Service Report Form</title>
    <style>
        @page { margin: 22px; }
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #111827; font-size: 11px; }
        .header-row { width: 100%; }
        .logo { width: 140px; }
        .title { text-align: center; font-size: 16px; font-weight: bold; margin: 6px 0 2px; }
        .company-line { text-align: center; font-size: 10px; margin: 2px 0; }
        .serial { text-align: right; font-size: 12px; font-weight: bold; }
        .box { border: 1px solid #111; padding: 0; margin-top: 6px; }
        .box table { width: 100%; border-collapse: collapse; }
        .box th, .box td { border: 1px solid #111; padding: 4px 6px; vertical-align: top; }
        .box th { background: #e5e7eb; font-size: 10px; text-transform: uppercase; letter-spacing: 0.06em; }
        .label { font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .value { font-size: 11px; }
        .section-title { background: #e5e7eb; font-weight: bold; text-transform: uppercase; font-size: 10px; letter-spacing: 0.06em; padding: 4px 6px; }
        .checkbox { display: inline-block; width: 10px; height: 10px; border: 1px solid #111; margin-right: 4px; text-align: center; line-height: 10px; font-size: 9px; }
        .selected { font-weight: bold; text-decoration: underline; }
        .signature { height: 40px; text-align: center; vertical-align: middle; }
        .centered { text-align: center; vertical-align: middle; }
        .small { font-size: 9px; }
    </style>
</head>
<body>
    @php
        $siteTime = $report->site_time_minutes
            ? sprintf('%dh %02dm', intdiv($report->site_time_minutes, 60), $report->site_time_minutes % 60)
            : '-';
        $charges = ['Charge', 'No Charge', 'Contract', 'Warranty', 'Installation', 'Routine'];
        $departureOptions = [
            'Operational/Conclusive',
            'Operational/Inconclusive',
            'Removal to Workshop',
            'Insufficient Spares',
            'Software Problem',
            'Requires further visit',
        ];
        $canRenderImages = extension_loaded('gd');
    @endphp

    <table class="header-row" cellpadding="0" cellspacing="0">
        <tr>
            <td class="logo">
                @if ($canRenderImages)
                    <img src="{{ public_path('images/transflo-logo.jpg') }}" alt="TransFlo" style="height: 50px;">
                @else
                    <strong>TransFlo</strong>
                @endif
            </td>
            <td>
                <div class="company-line">Unit 6, Rose Lane Industrial Estate, Lenham Heath, Kent ME17 2JN, UK</div>
                <div class="company-line">Telephone: +44 (0)1622 859564</div>
                <div class="company-line">Email: servicedesk@transflo.co.uk · www.transflo.co.uk</div>
            </td>
            <td class="serial">SRF No. {{ $report->serial_number }}</td>
        </tr>
    </table>

    <div class="title">Service Report Form</div>

    <div class="box">
        <table>
            <tr>
                <td style="width: 70%;">
                    <span class="label">Company</span>
                    <div class="value">{{ $report->company_name }}</div>
                </td>
                <td style="width: 30%;">
                    <span class="label">BFIS Plant ID</span>
                    <div class="value">{{ $report->plant?->plant_id }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <span class="label">Site Address</span>
                    <div class="value">{{ $report->site_address ?? '-' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="box">
        <table>
            <tr>
                <td style="width: 20%;">
                    <span class="label">Date of Visit</span>
                    <div class="value">{{ $report->date_of_visit?->format('d-m-Y') }}</div>
                </td>
                <td style="width: 16%;">
                    <span class="label">Time on Site</span>
                    <div class="value">{{ $report->time_on_site ?? '-' }}</div>
                </td>
                <td style="width: 16%;">
                    <span class="label">Time off Site</span>
                    <div class="value">{{ $report->time_off_site ?? '-' }}</div>
                </td>
                <td style="width: 16%;">
                    <span class="label">Mileage</span>
                    <div class="value">{{ $report->mileage ?? '-' }}</div>
                </td>
                <td style="width: 16%;">
                    <span class="label">Travel Time</span>
                    <div class="value">{{ $report->travel_time_hours ?? '-' }}</div>
                </td>
                <td style="width: 16%;">
                    <span class="label">Site Time</span>
                    <div class="value">{{ $siteTime }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="box">
        <table>
            <tr>
                <td style="width: 33%;">
                    <span class="label">Order No.</span>
                    <div class="value">{{ $report->order_number ?? '-' }}</div>
                </td>
                <td style="width: 33%;">
                    <span class="label">TransFlo Ref</span>
                    <div class="value">{{ $report->transflo_ref ?? '-' }}</div>
                </td>
                <td style="width: 34%;">
                    <span class="label">Equipment Type</span>
                    <div class="value">{{ $report->equipment_type }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="box">
        <div class="section-title">Charge / No Charge / Contract / Warranty / Installation / Routine</div>
        <div style="padding: 6px;" class="value">{{ strtoupper($report->charge_type) }}</div>
        <div class="section-title">Report</div>
        <table>
            <tr>
                <td style="width: 20%;" class="label">Equipment Type</td>
                <td>{{ $report->equipment_type }}</td>
            </tr>
            <tr>
                <td class="label">Reported Fault</td>
                <td>{{ $report->reported_fault ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Report Details</td>
                <td style="height: 120px; white-space: pre-line;">{{ $report->report_details ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="box">
        <div class="section-title">Ensure call details are entered into FuelManager notes form</div>
        <table>
            <tr>
                <td style="width: 50%;">
                    <div class="label">Departure Status</div>
                    @foreach ($departureOptions as $option)
                        <div>
                            <span class="checkbox">{{ $report->departure_statuses && in_array($option, $report->departure_statuses, true) ? 'X' : '' }}</span>
                            {{ $option }}
                        </div>
                    @endforeach
                </td>
                <td style="width: 25%;" class="centered">
                    <div class="label">Software Changes</div>
                    <div style="height: 60px; line-height: 60px;">{{ $report->software_changes ?? '-' }}</div>
                </td>
                <td style="width: 25%;" class="centered">
                    <div class="label">Engineers Signature</div>
                    <div class="signature" style="line-height: 40px;">
                        @if ($canRenderImages && $report->engineer_signature_path)
                            <img src="{{ public_path('storage/' . $report->engineer_signature_path) }}" style="height: 36px;">
                        @endif
                    </div>
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <th style="width: 40%;">Parts Supplied</th>
                <th style="width: 15%;">Stock Code</th>
                <th style="width: 10%;">No. Off</th>
                <th style="width: 15%;">Price Each</th>
                <th style="width: 20%;">Total Price</th>
            </tr>
            @forelse ($report->parts as $part)
                <tr>
                    <td>{{ $part->part_description ?? '-' }}</td>
                    <td>{{ $part->stock_code ?? '-' }}</td>
                    <td>{{ $part->quantity ?? '-' }}</td>
                    <td>{{ $part->price_each ?? '-' }}</td>
                    <td>{{ $part->total_price ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="height: 60px;"></td>
                </tr>
            @endforelse
        </table>
    </div>

    <div class="box">
        <table>
            <tr>
                <td colspan="3" class="small">The above work has been completed to our/my satisfaction</td>
            </tr>
            <tr>
                <td style="width: 33%;">
                    <div class="label">Signature</div>
                    <div class="signature">
                        @if ($canRenderImages && $report->customer_signature_path)
                            <img src="{{ public_path('storage/' . $report->customer_signature_path) }}" style="height: 36px;">
                        @endif
                    </div>
                </td>
                <td style="width: 34%;">
                    <div class="label">Print Name</div>
                    <div>{{ $report->customer_print_name ?? '-' }}</div>
                </td>
                <td style="width: 33%;">
                    <div class="label">Rank/Civ</div>
                    <div>{{ $report->customer_rank_civ ?? '-' }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="small" style="padding-top: 6px;">
                    Please note any comments you have regarding our service or equipment
                </td>
            </tr>
        </table>
    </div>

    <div class="small" style="margin-top: 6px; text-align: center;">
        This Service Report Form represents a true and accurate, signed copy, held by TransFlo Instruments Ltd
    </div>
    <div class="small" style="margin-top: 4px; text-align: center;">
        White - Service Copy - Return to TransFlo · Green - Customer Receipted Copy - Site to keep on local file
    </div>
</body>
</html>
