<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Inspection Report - {{ $report->inspectionRequest->request_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #222; margin: 0; padding: 0; }
        .container { max-width: 900px; margin: 0 auto; padding: 32px 24px 24px 24px; min-height: 100vh; position: relative; }
        .header { display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #0074c2; padding-bottom: 12px; margin-bottom: 24px; }
        .logo { height: 48px; }
        .header-info { font-size: 13px; text-align: right; }
        .header-info strong { display: inline-block; min-width: 110px; }
        h1 { font-size: 2rem; margin: 24px 0 16px 0; font-weight: bold; }
        .summary-table, .section-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        .summary-table th, .summary-table td, .section-table th, .section-table td { border: 1px solid #ddd; padding: 8px 10px; font-size: 14px; }
        .summary-table th { background: #f5f5f5; text-align: left; width: 220px; }
        .section-table th { background: #e8f4fa; }
        .section-title { font-size: 1.2rem; font-weight: bold; margin: 32px 0 12px 0; }
        .service-photo { max-width: 220px; max-height: 160px; margin-top: 8px; border: 1px solid #ccc; }
        .footer { font-size: 12px; color: #888; border-top: 1px solid #eee; padding-top: 12px; display: flex; justify-content: space-between; position: absolute; left: 24px; right: 24px; bottom: 24px; }
        .text-bold { font-weight: bold; }
        .text-center { text-align: center; }
        .mt-2 { margin-top: 8px; }
        .mb-2 { margin-bottom: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <img src="{{ public_path('logo-funditech.png') }}" alt="Fundi.info" class="logo">
            <div class="header-info">
                <div>Fundi Portal</div>
                <div>Requested Date : {{ $report->created_at ? $report->created_at->format('l, d F Y, h:i a') : 'N/A' }}</div>
                <div>Package: {{ $report->inspectionRequest->package->display_name ?? 'N/A' }}</div>
            </div>
        </div>

        <h1>Property Inspection Report</h1>

        <!-- Property Information Section -->
        <div style="border:1px solid #e5e7eb; border-radius:8px; padding:20px; margin-bottom:28px;">
            <div style="font-size:20px; font-weight:bold; margin-bottom:4px;">Property Information</div>
            <div style="color:#6b7280; font-size:13px; margin-bottom:16px;">Complete details about this property.</div>
            <!-- Owner Info and Type -->
            <div style="display:flex; justify-content:space-between; margin-bottom:12px;">
                <div>
                    <div style="font-size:11px; color:#9ca3af; margin-bottom:2px;">Owner Information</div>
                    <div style="font-weight:bold;">{{ $report->inspectionRequest->property->owner_name ?? 'N/A' }}</div>
                    <div style="font-size:13px; color:#374151; margin-top:2px;">📞 {{ $report->inspectionRequest->property->owner_phone ?? 'N/A' }}</div>
                    <div style="font-size:13px; color:#374151; margin-top:2px;">✉️ {{ $report->inspectionRequest->property->owner_email ?? 'N/A' }}</div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:11px; color:#9ca3af; margin-bottom:2px;">Property Type</div>
                    <span style="display:inline-block; padding:2px 10px; border-radius:12px; background:#dbeafe; color:#2563eb; font-size:12px; font-weight:600;">{{ $report->inspectionRequest->property->property_type ?? 'N/A' }}</span>
                    @if($report->inspectionRequest->property->property_subtype)
                        <span style="display:inline-block; padding:2px 10px; border-radius:12px; background:#f3f4f6; color:#374151; font-size:12px; font-weight:600; margin-left:6px;">{{ $report->inspectionRequest->property->property_subtype }}</span>
                    @endif
                </div>
            </div>
            <!-- Address -->
            <div style="margin-bottom:12px;">
                <div style="font-size:11px; color:#9ca3af; margin-bottom:2px;">Address</div>
                <div style="font-weight:500;">{{ $report->inspectionRequest->property->address ?? 'N/A' }}</div>
                <div style="font-size:13px; color:#6b7280;">{{ $report->inspectionRequest->property->cell }}, {{ $report->inspectionRequest->property->sector }}, {{ $report->inspectionRequest->property->district }}</div>
                <div style="font-size:12px; color:#6b7280; margin-top:2px;">
                    📍 {{ $report->inspectionRequest->property->latitude ?? 'N/A' }}, {{ $report->inspectionRequest->property->longitude ?? 'N/A' }}
                    @if(!empty($report->inspectionRequest->property->latitude) && !empty($report->inspectionRequest->property->longitude))
                        <span style="margin-left:8px;"><a href="https://maps.google.com/?q={{ $report->inspectionRequest->property->latitude }},{{ $report->inspectionRequest->property->longitude }}" style="color:#2563eb; text-decoration:underline;" target="_blank">View on Map</a></span>
                    @endif
                </div>
            </div>
            <!-- Specifications -->
            <div style="display:flex; justify-content:space-between; margin-bottom:12px;">
                <div>
                    <div style="font-size:11px; color:#9ca3af; margin-bottom:2px;">Specifications</div>
                    <div style="font-size:13px; color:#374151;">Year Built: <b>{{ $report->inspectionRequest->property->built_year ?? 'N/A' }}</b></div>
                    <div style="font-size:13px; color:#374151;">Floors: <b>{{ $report->inspectionRequest->property->floors_count ?? 'N/A' }}</b></div>
                    <div style="font-size:13px; color:#374151;">Bathrooms: <b>{{ $report->inspectionRequest->property->bathrooms_count ?? 'N/A' }}</b></div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:13px; color:#374151;">Total Area: <b>{{ $report->inspectionRequest->property->total_area_sqm ?? 'N/A' }} m<sup>2</sup></b></div>
                    <div style="font-size:13px; color:#374151;">Bedrooms: <b>{{ $report->inspectionRequest->property->bedrooms_count ?? 'N/A' }}</b></div>
                </div>
            </div>
            <!-- Additional Notes -->
            <div style="margin-bottom:8px;">
                <div style="font-size:11px; color:#9ca3af; margin-bottom:2px;">Additional Notes</div>
                <div style="font-size:13px; color:#374151;">{{ $report->inspectionRequest->property->additional_notes ?? 'N/A' }}</div>
            </div>
            <!-- Inspection Status -->
            <div>
                <div style="font-size:11px; color:#9ca3af; margin-bottom:2px;">Inspection Status</div>
                @php
                    $last = $report->inspectionRequest->property->last_inspection_date;
                    $needs = !$last || \Carbon\Carbon::parse($last)->lt(now()->subMonths(12));
                @endphp
                @if($needs)
                    <span style="display:inline-block; padding:2px 10px; border-radius:12px; background:#fee2e2; color:#b91c1c; font-size:12px; font-weight:600; margin-right:8px;">Needs Inspection</span>
                    <span style="font-size:13px; color:#374151;">This property hasn't been inspected in over 12 months or has never been inspected.</span>
                @else
                    @php
                        $monthsAgo = (int) \Carbon\Carbon::parse($last)->diffInMonths(now(), false);
                        if ($monthsAgo >= 12) {
                            $yearsAgo = intdiv($monthsAgo, 12);
                            $agoText = $yearsAgo . ' year' . ($yearsAgo > 1 ? 's' : '') . ' ago';
                        } else {
                            $agoText = $monthsAgo . ' month' . ($monthsAgo == 1 ? '' : 's') . ' ago';
                        }
                    @endphp
                    <span style="display:inline-block; padding:2px 10px; border-radius:12px; background:#dcfce7; color:#166534; font-size:12px; font-weight:600; margin-right:8px;">Inspected</span>
                    <span style="font-size:13px; color:#374151;">Last inspected on {{ \Carbon\Carbon::parse($last)->format('F d, Y') }} ({{ $agoText }})</span>
                @endif
            </div>
        </div>

        <!-- Summary Table -->
        <table class="summary-table">
            <tr><th>Request Number</th><td>{{ $report->inspectionRequest->request_number ?? 'N/A' }}</td></tr>
            <tr><th>Client Name</th><td>{{ $report->inspectionRequest->requester->full_name ?? 'N/A' }}</td></tr>
            <tr><th>Client Address</th><td>{{ $report->inspectionRequest->property->address ?? 'N/A' }}</td></tr>
            <tr><th>Client Phone Number</th><td>{{ $report->inspectionRequest->requester->phone_number ?? 'N/A' }}</td></tr>
            <tr><th>Inspector Name</th><td>{{ $report->inspector->user->full_name ?? 'N/A' }}</td></tr>
            <tr><th>Inspector Contact</th><td>{{ $report->inspector->user->phone ?? 'N/A' }}</td></tr>
            <tr><th>Date of Inspection</th><td>{{ $report->inspectionRequest->scheduled_date ? $report->inspectionRequest->scheduled_date->format('l, d F Y') : 'N/A' }}</td></tr>
            <tr><th>Date of Report</th><td>{{ $report->completed_at ? $report->completed_at->format('l, d F Y') : 'N/A' }}</td></tr>
        </table>

        <!-- Service Findings Section -->
        <div class="section-title">Service Findings</div>
        <table class="section-table">
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Findings / Notes</th>
                    <th>Photo</th>
                </tr>
            </thead>
            <tbody>
            @foreach($services as $service)
                <tr>
                    <td>{{ $service->name }}</td>
                    <td>{{ $report->data['service_notes_'.$service->id] ?? 'No notes provided.' }}</td>
                    <td>
                        @if(isset($report->data['service_photo_'.$service->id]))
                            <img src="{{ storage_path('app/public/' . $report->data['service_photo_'.$service->id]) }}" alt="Service Photo" class="service-photo">
                        @else
                            <span class="text-center">-</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <!-- General Comments -->
        <div class="section-title">General Comments / Summary</div>
        <div class="mb-2">{{ $report->data['general_comments'] ?? 'No general comments.' }}</div>

        <!-- Footer -->
        <div class="footer">
            <span>Generated by Fundi.info</span>
            <span>This PDF was created at {{ now()->format('l, d F Y, h:i a') }}</span>
        </div>
    </div>
</body>
</html> 