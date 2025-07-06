@extends('layouts.business-partner')

@section('content')
<div class="py-8 max-w-4xl mx-auto">
    <!-- Header with Download Button -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold mb-2">Inspection Report</h1>
                <div class="mb-4 text-gray-600">Request #: <span class="font-semibold">{{ $report->inspectionRequest->request_number }}</span></div>
            </div>
            @if($report->status === 'completed')
                <a href="{{ route('business-partner.reports.download', $report->id) }}" 
                   class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg transition-colors duration-200 shadow-sm"
                   title="Download PDF Report">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download PDF
                </a>
            @else
                <span class="inline-flex items-center bg-gray-300 text-gray-500 font-semibold px-6 py-3 rounded-lg cursor-not-allowed"
                      title="Report not completed yet">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download PDF
                </span>
            @endif
        </div>
        
        <!-- Report Details Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="font-semibold text-gray-700 mb-3">Property Information</h3>
                <div class="space-y-2 text-sm">
                    <div><span class="font-medium">Address:</span> {{ $report->inspectionRequest->property->address ?? '-' }}</div>
                    <div><span class="font-medium">Property Code:</span> {{ $report->inspectionRequest->property->property_code ?? '-' }}</div>
                </div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="font-semibold text-gray-700 mb-3">Inspection Details</h3>
                <div class="space-y-2 text-sm">
                    <div><span class="font-medium">Client:</span> {{ $report->inspectionRequest->requester->full_name ?? '-' }}</div>
                    <div><span class="font-medium">Package:</span> {{ $report->inspectionRequest->package->display_name ?? '-' }}</div>
                    <div><span class="font-medium">Inspector:</span> {{ $report->inspectionRequest->inspector->user->full_name ?? '-' }}</div>
                </div>
            </div>
        </div>
        
        <!-- Status and Completion Info -->
        <div class="mt-4 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if($report->status === 'completed') bg-green-100 text-green-800
                    @elseif($report->status === 'draft') bg-yellow-100 text-yellow-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    {{ ucfirst($report->status) }}
                </span>
                @if($report->completed_at)
                    <span class="text-sm text-gray-600">
                        Completed: {{ $report->completed_at->format('M d, Y H:i') }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Report Content -->
    <div class="bg-white shadow rounded-lg p-8 mb-6">
        <h2 class="text-xl font-semibold mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Report Details
        </h2>
        @if(is_array($report->data))
            <div class="space-y-4">
                @foreach($report->data as $key => $value)
                    <div class="border-b border-gray-200 pb-4 last:border-b-0">
                        <h4 class="font-semibold text-gray-700 mb-2">{{ ucwords(str_replace('_', ' ', $key)) }}</h4>
                        @if(Str::endsWith($key, 'photo') && $value)
                            <img src="{{ asset('storage/' . $value) }}" alt="Photo" class="mt-2 rounded-lg shadow-md max-w-md">
                        @else
                            <p class="text-gray-600">{{ $value ?: 'No data provided' }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-gray-500">No report data available.</p>
            </div>
        @endif
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center space-x-4">
        <a href="{{ route('business-partner.reports.index') }}" 
           class="inline-flex items-center bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-6 py-3 rounded-lg transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Reports
        </a>
        @if($report->status === 'completed')
            <a href="{{ route('business-partner.reports.download', $report->id) }}" 
               class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-3 rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Download PDF
            </a>
        @endif
    </div>
</div>
@endsection 