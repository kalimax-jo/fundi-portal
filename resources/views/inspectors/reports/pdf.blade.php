<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Inspection Report - {{ $report->inspectionRequest->request_number }}</title>
    <style>
        body { 
            font-family: Arial, sans-serif;
            color: #000;
            background: #fff;
            line-height: 1.2;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }
        
        .container { 
            width: 100%;
            border: 2px solid #000;
        }
        
        .header {
            text-align: center;
            padding: 20px;
            border-bottom: 2px solid #000;
        }
        
        .header-title {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }
        
        .header-subtitle {
            font-size: 12px;
            margin-bottom: 3px;
        }
        
        .document-info-table {
            width: 100%;
            font-size: 9px;
            margin-top: 15px;
        }
        
        .certificate-title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 15px 0;
            padding: 12px 0;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }
        
        .content {
            padding: 0 20px 20px 20px;
        }
        
        .main-section-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        .main-section-table td {
            vertical-align: top;
            padding: 0 15px;
        }
        
        .section-header {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 10px;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
        }
        
        .info-item {
            margin-bottom: 6px;
            font-size: 10px;
        }
        
        .info-label {
            color: #666;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .info-value {
            color: #000;
            margin-bottom: 8px;
        }
        
        .tag {
            display: inline-block;
            padding: 2px 6px;
            background: #e0e7ff;
            color: #3730a3;
            border: 1px solid #c7d2fe;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        
        .section-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 15px 0 10px 0;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
        }
        
        .property-info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            table-layout: fixed;
        }
        
        .property-info-table td {
            vertical-align: top;
            padding: 0 10px 0 0;
            width: 50%;
        }
        
        .client-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            margin: 10px 0;
        }
        
        .client-table th {
            background: #f8f8f8;
            padding: 8px;
            border: 1px solid #000;
            font-weight: bold;
            font-size: 10px;
            text-align: left;
            width: 35%;
        }
        
        .client-table td {
            padding: 8px;
            border: 1px solid #000;
            font-size: 10px;
            width: 65%;
        }
        
        .findings-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            margin: 10px 0;
        }
        
        .findings-table th {
            background: #f8f8f8;
            padding: 8px;
            border: 1px solid #000;
            font-weight: bold;
            font-size: 10px;
            text-align: left;
        }
        
        .findings-table td {
            padding: 8px;
            border: 1px solid #000;
            font-size: 9px;
            vertical-align: top;
        }
        
        .findings-table th:nth-child(1),
        .findings-table td:nth-child(1) {
            width: 30%;
        }
        
        .findings-table th:nth-child(2),
        .findings-table td:nth-child(2) {
            width: 55%;
        }
        
        .findings-table th:nth-child(3),
        .findings-table td:nth-child(3) {
            width: 110px;
            text-align: center;
            padding: 4px;
        }
        
        .service-photo {
            max-width: 50px;
            max-height: 40px;
            border: 1px solid #000;
            display: block;
            margin: 2px auto;
        }
        
        .photo-link {
            display: block;
            text-align: center;
            text-decoration: none;
            color: #000;
        }
        
        .photo-link:hover {
            opacity: 0.8;
        }
        
        .photo-text {
            font-size: 8px;
            color: #666;
            margin-top: 2px;
        }
        
        .assessment-box {
            background: #f8f8f8;
            border: 1px solid #000;
            padding: 12px;
            margin: 10px 0;
            font-size: 10px;
        }
        
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            border-top: 1px solid #000;
            padding-top: 15px;
        }
        
        .signature-table td {
            text-align: center;
            vertical-align: top;
            padding: 0 20px;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            height: 30px;
            margin: 20px 0 8px 0;
        }
        
        .signature-label {
            font-size: 9px;
            color: #666;
        }
        
        .footer {
            background: #f8f8f8;
            padding: 12px 20px;
            border-top: 1px solid #000;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
        
        .verification-info {
            margin-top: 6px;
            font-size: 8px;
        }
        
        .document-number {
            font-weight: bold;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <div class="header-title">Fundi.info</div>
            <div class="header-subtitle">Professional Property Inspection Services</div>
            <div class="header-subtitle">Certified Property Assessment Platform</div>
            
            <table class="document-info-table">
                <tr>
                    <td style="text-align: left;">
                        <strong>Issued by:</strong> Fundi.info Property Inspectors<br>
                        <strong>On:</strong> {{ $report->created_at ? $report->created_at->format('d/m/Y') : 'N/A' }}
                    </td>
                    <td style="text-align: right;">
                        <strong>Report validity</strong><br>
                        <strong>Until:</strong> {{ $report->created_at ? $report->created_at->addYear()->format('d/m/Y') : 'N/A' }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="content">
            <!-- Certificate Title -->
            <div class="certificate-title">Property Inspection Report</div>

            <!-- Main Information Section - Two Columns Using Table (NO VERTICAL LINE) -->
            <table class="main-section-table">
                <tr>
                    <td style="width: 50%;">
                        <div class="section-header">Property Owner</div>
                        
                        <div class="info-item">
                            <div class="info-label">Name:</div>
                            <div class="info-value">{{ $report->inspectionRequest->property->owner_name ?? 'N/A' }}</div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Owner Phone:</div>
                            <div class="info-value">{{ $report->inspectionRequest->property->owner_phone ?? 'N/A' }}</div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Owner Email:</div>
                            <div class="info-value">{{ $report->inspectionRequest->property->owner_email ?? 'N/A' }}</div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Property Type:</div>
                            <div class="info-value">
                                <span class="tag">{{ ucfirst($report->inspectionRequest->property->property_type ?? 'N/A') }}</span>
                            </div>
                        </div>
                    </td>
                    <td style="width: 50%;">
                        <div class="section-header">Report Details</div>
                        
                        <div class="info-item">
                            <div class="info-label">Report No:</div>
                            <div class="info-value"><strong>{{ $report->inspectionRequest->request_number ?? 'N/A' }}</strong></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Client Name:</div>
                            <div class="info-value">{{ $report->inspectionRequest->requester->full_name ?? 'N/A' }}</div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Inspection Date:</div>
                            <div class="info-value">{{ $report->inspectionRequest->scheduled_date ? $report->inspectionRequest->scheduled_date->format('d/m/Y') : 'N/A' }}</div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Inspector:</div>
                            <div class="info-value">{{ $report->inspector->user->full_name ?? 'N/A' }}</div>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- Property Information Section -->
            <div class="section-title">Property Information</div>
            <table class="property-info-table">
                <tr>
                    <td style="width: 50%; vertical-align: top;">
                        <div class="info-item">
                            <div class="info-label">Address:</div>
                            <div class="info-value">{{ $report->inspectionRequest->property->address ?? 'N/A' }}</div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Location:</div>
                            <div class="info-value">{{ $report->inspectionRequest->property->cell ?? 'N/A' }}, {{ $report->inspectionRequest->property->sector ?? 'N/A' }}, {{ $report->inspectionRequest->property->district ?? 'N/A' }}</div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Construction Year:</div>
                            <div class="info-value">{{ $report->inspectionRequest->property->built_year ?? 'N/A' }}</div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Total Area:</div>
                            <div class="info-value">{{ $report->inspectionRequest->property->total_area_sqm ? number_format($report->inspectionRequest->property->total_area_sqm, 2) . ' m²' : 'N/A m²' }}</div>
                        </div>
                    </td>
                    <td style="width: 50%; vertical-align: top;">
                        <div class="info-item">
                            <div class="info-label">Floors:</div>
                            <div class="info-value">{{ $report->inspectionRequest->property->floors_count ?? 'N/A' }}</div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Bedrooms:</div>
                            <div class="info-value">{{ $report->inspectionRequest->property->bedrooms_count ?? 'N/A' }}</div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Bathrooms:</div>
                            <div class="info-value">{{ $report->inspectionRequest->property->bathrooms_count ?? 'N/A' }}</div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Coordinates:</div>
                            <div class="info-value">{{ $report->inspectionRequest->property->latitude ?? 'N/A' }}, {{ $report->inspectionRequest->property->longitude ?? 'N/A' }}</div>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- Client & Inspector Details Section -->
            <div class="section-title">Client & Inspector Details</div>
            <table class="client-table">
                <tr><th>Client Name</th><td>{{ $report->inspectionRequest->requester->full_name ?? 'N/A' }}</td></tr>
                <tr><th>Client Phone Number</th><td>{{ $report->inspectionRequest->requester->phone_number ?? 'N/A' }}</td></tr>
                <tr><th>Inspector Name</th><td>{{ $report->inspector->user->full_name ?? 'N/A' }}</td></tr>
                <tr><th>Inspector Contact</th><td>{{ $report->inspector->user->phone ?? 'N/A' }}</td></tr>
                <tr><th>Inspection Package</th><td>{{ $report->inspectionRequest->package->display_name ?? 'N/A' }}</td></tr>
                <tr><th>Date of Report</th><td>{{ $report->completed_at ? $report->completed_at->format('l, d F Y') : 'N/A' }}</td></tr>
            </table>

            <!-- Service Findings Section -->
            @if(isset($services) && count($services) > 0)
            <div class="section-title">Inspection Results</div>
            <table class="findings-table">
                <thead>
                    <tr>
                        <th>Service Category</th>
                        <th>Findings / Notes</th>
                        <th>Photo</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($services as $service)
                    <tr>
                        <td><strong>{{ $service->name }}</strong></td>
                        <td>{{ $report->data['service_notes_'.$service->id] ?? 'No notes provided.' }}</td>
                        <td>
                            @if(isset($report->data['service_photo_'.$service->id]))
                                @php
                                    $photoPath = $report->data['service_photo_'.$service->id];
                                    $photoUrl = asset('storage/' . $photoPath);
                                @endphp
                                <a href="{{ $photoUrl }}" class="photo-link" target="_blank">
                                    <img src="{{ storage_path('app/public/' . $photoPath) }}" alt="Service Photo" class="service-photo">
                                    <div class="photo-text">Click to view</div>
                                </a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @endif

            <!-- Overall Assessment Section -->
            <div class="section-title">Overall Assessment</div>
            <div class="assessment-box">
                <strong>General Comments:</strong><br>
                {{ $report->data['general_comments'] ?? 'No general comments provided.' }}
                <br><br>
                <strong>Report Summary:</strong> This property has been thoroughly inspected by our certified professionals and assessed according to industry best practices and construction standards. This inspection report is valid for one year from the date of issue and provides a comprehensive assessment of the property condition.
            </div>

            <!-- Signature Section -->
            <table class="signature-table">
                <tr>
                    <td>
                        <div class="signature-line"></div>
                        <div class="signature-label">
                            <strong>Property Inspector</strong><br>
                            {{ $report->inspector->user->full_name ?? 'N/A' }}<br>
                            License No: {{ $report->inspector->license_number ?? 'INS-2023-' . ($report->inspector->id ?? '000') }}
                        </div>
                    </td>
                    <td>
                        <div class="signature-line"></div>
                        <div class="signature-label">
                            <strong>Certified Inspector</strong><br>
                            Property Inspection Department<br>
                            Date: {{ $report->completed_at ? $report->completed_at->format('d/m/Y') : now()->format('d/m/Y') }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Footer Section -->
        <div class="footer">
            <div>
                <strong>Report issued by Fundi.info</strong><br>
                Professional Property Inspection Services
            </div>
            <div class="verification-info">
                For verification and more information, visit <strong>https://fundi.info/</strong><br>
                Report Code: <span class="document-number">F{{ $report->created_at ? $report->created_at->format('ymdHis') : now()->format('ymdHis') }}{{ $report->id ?? '000' }}</span> | Contact: <strong>info@fundi.info</strong>
            </div>
        </div>
    </div>
</body>
</html>