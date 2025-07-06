<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Inspection Report - {{ $report->inspectionRequest->request_number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        @media print {
            body { margin: 0; }
            .container { padding: 8px; }
            .no-print { display: none; }
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body { 
            font-family: 'Inter', sans-serif;
            color: #2d3748;
            background: #ffffff;
            line-height: 1.5;
        }
        
        .container { 
            max-width: 800px; 
            margin: 20px auto; 
            background: white;
            border: 2px solid #2d3748;
            position: relative;
        }
        
        .header-section {
            text-align: center;
            padding: 30px 40px 20px;
            border-bottom: 2px solid #2d3748;
        }
        
        .header-title {
            font-size: 24px;
            font-weight: 700;
            color: #2d3748;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }
        
        .header-subtitle {
            font-size: 18px;
            font-weight: 500;
            color: #2d3748;
            margin-bottom: 20px;
        }
        
        .document-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            font-size: 12px;
            color: #4a5568;
        }
        
        .certificate-title {
            font-size: 28px;
            font-weight: 700;
            color: #2d3748;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 30px 0;
            padding: 20px 0;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .content-section {
            padding: 0 40px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin: 30px 0;
        }
        
        .info-column h3 {
            font-size: 16px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .info-label {
            font-weight: 500;
            color: #4a5568;
            min-width: 120px;
            margin-right: 10px;
        }
        
        .info-value {
            font-weight: 400;
            color: #2d3748;
            flex: 1;
        }
        
        .full-width-section {
            margin: 30px 0;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .findings-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            border: 1px solid #e2e8f0;
        }
        
        .findings-table th {
            background: #f8fafc;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #2d3748;
            font-size: 14px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .findings-table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 13px;
            vertical-align: top;
        }
        
        .findings-table tr:last-child td {
            border-bottom: none;
        }
        
        .modern-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border: 1px solid #e2e8f0;
        }
        
        .modern-table th {
            background: #f8fafc;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #2d3748;
            font-size: 14px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .modern-table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }
        
        .modern-table tr:last-child td {
            border-bottom: none;
        }
        
        .service-photo {
            max-width: 150px;
            max-height: 100px;
            margin-top: 8px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
        }
        
        .assessment-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 20px;
            margin: 20px 0;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .signature-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin: 40px 0;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }
        
        .signature-box {
            text-align: center;
        }
        
        .signature-line {
            border-bottom: 1px solid #2d3748;
            margin: 30px 0 10px;
            height: 40px;
        }
        
        .signature-label {
            font-size: 12px;
            color: #4a5568;
            font-weight: 500;
        }
        
        .footer-section {
            background: #f8fafc;
            padding: 20px 40px;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            color: #4a5568;
            text-align: center;
        }
        
        .verification-info {
            margin-top: 10px;
            font-size: 11px;
        }
        
        .document-number {
            font-weight: 600;
            color: #2d3748;
        }
        
        .tag {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            margin-right: 8px;
            margin-bottom: 4px;
        }
        
        .tag-primary {
            background: #e0e7ff;
            color: #3730a3;
            border: 1px solid #c7d2fe;
        }
        
        .tag-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #9ae6b4;
        }
        
        .tag-warning {
            background: #fed7aa;
            color: #9a3412;
            border: 1px solid #f6e05e;
        }
        
        .tag-danger {
            background: #fecaca;
            color: #991b1b;
            border: 1px solid #fc8181;
        }
        
        .tag-info {
            background: #f0f9ff;
            color: #0c4a6e;
            border: 1px solid #7dd3fc;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header-section">
            <div class="header-title">Fundi.info</div>
            <div class="header-subtitle">Professional Property Inspection Services</div>
            <div class="header-subtitle">Certified Property Assessment Platform</div>
            
            <div class="document-info">
                <div>
                    <strong>Issued by:</strong> Fundi.info Property Inspectors<br>
                    <strong>On:</strong> {{ $report->created_at ? $report->created_at->format('d/m/Y') : 'N/A' }}
                </div>
                <div style="text-align: right;">
                    <strong>Report validity</strong><br>
                    <strong>Until:</strong> {{ $report->created_at ? $report->created_at->addYear()->format('d/m/Y') : 'N/A' }}
                </div>
            </div>
        </div>

        <div class="content-section">
            <!-- Certificate Title -->
            <div class="certificate-title">Property Inspection Report</div>

            <!-- Basic Information Grid -->
            <div class="info-grid">
                <div class="info-column">
                    <h3>Property Owner</h3>
                    <div class="info-row">
                        <span class="info-label">Name:</span>
                        <span class="info-value">{{ $report->inspectionRequest->property->owner_name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Owner Phone:</span>
                        <span class="info-value">{{ $report->inspectionRequest->property->owner_phone ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Owner Email:</span>
                        <span class="info-value">{{ $report->inspectionRequest->property->owner_email ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Property Type:</span>
                        <span class="info-value">
                            <span class="tag tag-primary">{{ $report->inspectionRequest->property->property_type ?? 'N/A' }}</span>
                            @if($report->inspectionRequest->property->property_subtype)
                                <span class="tag tag-info">{{ $report->inspectionRequest->property->property_subtype }}</span>
                            @endif
                        </span>
                    </div>
                </div>

                <div class="info-column">
                    <h3>Report Details</h3>
                    <div class="info-row">
                        <span class="info-label">Report No:</span>
                        <span class="info-value document-number">{{ $report->inspectionRequest->request_number ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Client Name:</span>
                        <span class="info-value">{{ $report->inspectionRequest->requester->full_name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Inspection Date:</span>
                        <span class="info-value">{{ $report->inspectionRequest->scheduled_date ? $report->inspectionRequest->scheduled_date->format('d/m/Y') : 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Inspector:</span>
                        <span class="info-value">{{ $report->inspector->user->full_name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Property Information -->
            <div class="full-width-section">
                <div class="section-title">Property Information</div>
                <div class="info-grid">
                    <div class="info-column">
                        <div class="info-row">
                            <span class="info-label">Address:</span>
                            <span class="info-value">{{ $report->inspectionRequest->property->address ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Location:</span>
                            <span class="info-value">{{ $report->inspectionRequest->property->cell ?? 'N/A' }}, {{ $report->inspectionRequest->property->sector ?? 'N/A' }}, {{ $report->inspectionRequest->property->district ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Construction Year:</span>
                            <span class="info-value">{{ $report->inspectionRequest->property->built_year ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Total Area:</span>
                            <span class="info-value">{{ $report->inspectionRequest->property->total_area_sqm ?? 'N/A' }} m²</span>
                        </div>
                    </div>
                    <div class="info-column">
                        <div class="info-row">
                            <span class="info-label">Floors:</span>
                            <span class="info-value">{{ $report->inspectionRequest->property->floors_count ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Bedrooms:</span>
                            <span class="info-value">{{ $report->inspectionRequest->property->bedrooms_count ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Bathrooms:</span>
                            <span class="info-value">{{ $report->inspectionRequest->property->bathrooms_count ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Coordinates:</span>
                            <span class="info-value">{{ $report->inspectionRequest->property->latitude ?? 'N/A' }}, {{ $report->inspectionRequest->property->longitude ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
                
                @if($report->inspectionRequest->property->additional_notes)
                <div class="info-row" style="margin-top: 15px;">
                    <span class="info-label">Additional Notes:</span>
                    <span class="info-value">{{ $report->inspectionRequest->property->additional_notes }}</span>
                </div>
                @endif
            </div>

            <!-- Client Information -->
            <div class="full-width-section">
                <div class="section-title">Client & Inspector Details</div>
                <table class="modern-table">
                    <tr><th>Client Name</th><td>{{ $report->inspectionRequest->requester->full_name ?? 'N/A' }}</td></tr>
                    <tr><th>Client Phone Number</th><td>{{ $report->inspectionRequest->requester->phone_number ?? 'N/A' }}</td></tr>
                    <tr><th>Inspector Name</th><td>{{ $report->inspector->user->full_name ?? 'N/A' }}</td></tr>
                    <tr><th>Inspector Contact</th><td>{{ $report->inspector->user->phone ?? 'N/A' }}</td></tr>
                    <tr><th>Inspection Package</th><td>{{ $report->inspectionRequest->package->display_name ?? 'N/A' }}</td></tr>
                    <tr><th>Date of Report</th><td>{{ $report->completed_at ? $report->completed_at->format('l, d F Y') : 'N/A' }}</td></tr>
                </table>
            </div>

            <!-- Service Findings -->
            @if(isset($services) && count($services) > 0)
            <div class="full-width-section">
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
                                    <img src="{{ storage_path('app/public/' . $report->data['service_photo_'.$service->id]) }}" alt="Service Photo" class="service-photo">
                                @else
                                    <span style="text-align: center;">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <!-- Overall Assessment -->
            <div class="full-width-section">
                <div class="section-title">Overall Assessment</div>
                <div class="assessment-box">
                    <strong>General Comments:</strong><br>
                    {{ $report->data['general_comments'] ?? 'No general comments provided.' }}
                    <br><br>
                    <strong>Report Summary:</strong> This property has been thoroughly inspected by our certified professionals and assessed according to industry best practices and construction standards. This inspection report is valid for one year from the date of issue and provides a comprehensive assessment of the property condition.
                </div>
            </div>

            <!-- Signature Section -->
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-label">
                        <strong>Property Inspector</strong><br>
                        {{ $report->inspector->user->full_name ?? 'N/A' }}<br>
                        License No: {{ $report->inspector->license_number ?? 'INS-2023-' . ($report->inspector->id ?? '000') }}
                    </div>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-label">
                        <strong>Certified Inspector</strong><br>
                        Property Inspection Department<br>
                        Date: {{ $report->completed_at ? $report->completed_at->format('d/m/Y') : now()->format('d/m/Y') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Section -->
        <div class="footer-section">
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