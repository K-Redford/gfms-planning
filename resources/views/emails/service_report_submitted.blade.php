<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Service Report Form Submitted</title>
    </head>
    <body style="font-family: Arial, sans-serif; color: #1f2937;">
        <h2 style="margin: 0 0 12px;">Service Report Form Submitted</h2>
        <p style="margin: 0 0 12px;">A Service Report Form has been submitted.</p>
        <table style="border-collapse: collapse; width: 100%; max-width: 600px;">
            <tr>
                <td style="padding: 6px 0; font-weight: bold;">Serial</td>
                <td style="padding: 6px 0;">{{ $report->serial_number }}</td>
            </tr>
            <tr>
                <td style="padding: 6px 0; font-weight: bold;">GFMS Plant ID</td>
                <td style="padding: 6px 0;">{{ $report->plant?->plant_id }}</td>
            </tr>
            <tr>
                <td style="padding: 6px 0; font-weight: bold;">Company</td>
                <td style="padding: 6px 0;">{{ $report->company_name }}</td>
            </tr>
            <tr>
                <td style="padding: 6px 0; font-weight: bold;">Date of Visit</td>
                <td style="padding: 6px 0;">{{ $report->date_of_visit?->format('Y-m-d') }}</td>
            </tr>
            <tr>
                <td style="padding: 6px 0; font-weight: bold;">Engineer</td>
                <td style="padding: 6px 0;">{{ $report->engineer?->name }}</td>
            </tr>
        </table>
        <p style="margin: 16px 0 0;">Log in to view the full report.</p>
    </body>
</html>
