@extends('layouts.inspector')

@section('title', 'Inspection Report')

@section('content')
    <a href="{{ route('inspector.assignments') }}" class="inline-block mb-4 text-indigo-600 hover:underline">&larr; Back to Assignments</a>
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h1 class="text-2xl font-bold mb-2">Inspection Report for Request #{{ $request->request_number }}</h1>
        <div class="text-sm text-gray-500 mb-1">Property: <span class="text-gray-700">{{ $request->property->address ?? '-' }}</span></div>
        <div class="text-sm text-gray-500 mb-1">Client: <span class="text-gray-700">{{ $request->requester->full_name ?? '-' }}</span></div>
        <div class="text-sm text-gray-500 mb-1">Package: <span class="text-gray-700">{{ $request->package->display_name ?? '-' }}</span></div>
    </div>

    @php
        $isCompleted = $report->status === 'completed';
        $isEditMode = request()->has('edit') && $isCompleted;
        $isViewMode = $isCompleted && !$isEditMode;
        $fieldsDisabled = $isViewMode ? 'disabled' : '';
    @endphp

    <form id="inspection-report-form" method="POST" action="{{ $isEditMode ? route('inspector.reports.update', $report->id) : route('inspector.reports.complete', $report->id) }}" enctype="multipart/form-data">
        @csrf
        @if($isEditMode)
            @method('PUT')
        @endif
        <input type="hidden" name="data" id="report-data" />

        <!-- Property Details Section -->
        <div class="bg-white border rounded-lg p-6 mb-6">
            <h2 class="font-semibold text-xl mb-2">Property Information</h2>
            <div class="text-gray-500 mb-4">Complete details about this property.</div>
            <!-- Owner Info -->
            <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="text-xs text-gray-400 mb-1">Owner Information</div>
                    <div class="font-semibold">{{ $request->property->owner_name ?? 'N/A' }}</div>
                    <div class="flex items-center text-sm text-gray-600 mt-1">
                        <svg class="w-4 h-4 mr-1 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        {{ $request->property->owner_phone ?? 'N/A' }}
                    </div>
                    <div class="flex items-center text-sm text-gray-600 mt-1">
                        <svg class="w-4 h-4 mr-1 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 01-8 0m8 0a4 4 0 00-8 0m8 0V8a4 4 0 00-8 0v4m8 0v4a4 4 0 01-8 0v-4"/></svg>
                        {{ $request->property->owner_email ?? 'N/A' }}
                    </div>
                </div>
                <div>
                    <div class="text-xs text-gray-400 mb-1">Property Type</div>
                    <span class="inline-block px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">{{ $request->property->property_type ?? 'N/A' }}</span>
                    @if($request->property->property_subtype)
                        <span class="inline-block px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-xs font-semibold ml-2">{{ $request->property->property_subtype }}</span>
                    @endif
                </div>
            </div>
            <!-- Address -->
            <div class="mb-4">
                <div class="text-xs text-gray-400 mb-1">Address</div>
                <div class="font-medium">{{ $request->property->address ?? 'N/A' }}</div>
                <div class="text-sm text-gray-500">{{ $request->property->cell }}, {{ $request->property->sector }}, {{ $request->property->district }}</div>
                <div class="flex items-center text-xs text-gray-500 mt-1">
                    <svg class="w-4 h-4 mr-1 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 21l-4.243-4.243A8 8 0 1117.657 16.657z"/></svg>
                    {{ $request->property->latitude ?? 'N/A' }}, {{ $request->property->longitude ?? 'N/A' }}
                    @if(!empty($request->property->latitude) && !empty($request->property->longitude))
                        <a href="https://maps.google.com/?q={{ $request->property->latitude }},{{ $request->property->longitude }}" target="_blank" class="ml-2 text-blue-600 underline">View on Map</a>
                    @endif
                </div>
            </div>
            <!-- Specifications -->
            <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="text-xs text-gray-400 mb-1">Specifications</div>
                    <div class="flex flex-wrap gap-x-8 gap-y-1">
                        <div>Year Built: <span class="font-medium">{{ $request->property->built_year ?? 'N/A' }}</span></div>
                        <div>Floors: <span class="font-medium">{{ $request->property->floors_count ?? 'N/A' }}</span></div>
                        <div>Bathrooms: <span class="font-medium">{{ $request->property->bathrooms_count ?? 'N/A' }}</span></div>
                    </div>
                </div>
                <div>
                    <div class="flex flex-wrap gap-x-8 gap-y-1 mt-6 md:mt-0">
                        <div>Total Area: <span class="font-medium">{{ $request->property->total_area_sqm ?? 'N/A' }} m<sup>2</sup></span></div>
                        <div>Bedrooms: <span class="font-medium">{{ $request->property->bedrooms_count ?? 'N/A' }}</span></div>
                    </div>
                </div>
            </div>
            <!-- Additional Notes -->
            <div class="mb-2">
                <div class="text-xs text-gray-400 mb-1">Additional Notes</div>
                <div>{{ $request->property->additional_notes ?? 'N/A' }}</div>
            </div>
            <!-- Inspection Status -->
            <div class="mb-2">
                <div class="text-xs text-gray-400 mb-1">Inspection Status</div>
                @php
                    $last = $request->property->last_inspection_date;
                    $needs = !$last || \Carbon\Carbon::parse($last)->lt(now()->subMonths(12));
                @endphp
                @if($needs)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mr-2">Needs Inspection</span>
                    <span class="text-sm text-gray-600">This property hasn't been inspected in over 12 months or has never been inspected.</span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-2">Inspected</span>
                    @php
                        $monthsAgo = (int) \Carbon\Carbon::parse($last)->diffInMonths(now(), false);
                        if ($monthsAgo >= 12) {
                            $yearsAgo = intdiv($monthsAgo, 12);
                            $agoText = $yearsAgo . ' year' . ($yearsAgo > 1 ? 's' : '') . ' ago';
                        } else {
                            $agoText = $monthsAgo . ' month' . ($monthsAgo == 1 ? '' : 's') . ' ago';
                        }
                    @endphp
                    <span class="text-sm text-gray-600">Last inspected on {{ \Carbon\Carbon::parse($last)->format('F d, Y') }} ({{ $agoText }})</span>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            @foreach($services as $service)
            <div class="bg-white border rounded-lg p-4 mb-4 flex flex-col md:flex-row gap-4 items-start">
                @if(isset($report->data['service_photo_'.$service->id]))
                    <div class="w-full md:w-1/3 flex-shrink-0">
                        <img src="{{ asset('storage/' . $report->data['service_photo_'.$service->id]) }}" alt="Service Photo" style="max-width: 100%; max-height: 180px; border: 1px solid #ccc; border-radius: 6px;">
                    </div>
                @endif
                <div class="flex-1 w-full">
                    <h2 class="font-semibold text-lg mb-2">{{ $service->name }} <span class="text-xs text-gray-400">({{ $service->getCategoryDisplayName() }})</span></h2>
                    <div class="mb-2 text-sm text-gray-500">{{ $service->description }}</div>
                    <textarea class="w-full border rounded px-3 py-2" rows="3" name="service_notes_{{ $service->id }}" placeholder="Enter findings, notes, or results..." {{ $fieldsDisabled }}>{{ $report->data['service_notes_'.$service->id] ?? '' }}</textarea>
                    <div class="mt-2">
                        @if(!$fieldsDisabled)
                        <label for="service_photo_{{ $service->id }}" class="text-sm font-medium text-gray-700">Take or Upload Photo (Optional)</label>
                        <input type="file" accept="image/*" capture="environment" name="service_photo_{{ $service->id }}" id="service_photo_{{ $service->id }}" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" {{ $fieldsDisabled }}/>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
            <div class="bg-white border rounded-lg p-4">
                <label class="block font-semibold mb-2">General Comments / Summary</label>
                <textarea class="w-full border rounded px-3 py-2" rows="4" name="general_comments" placeholder="Summary, recommendations, or additional notes..." {{ $fieldsDisabled }}>{{ $report->data['general_comments'] ?? '' }}</textarea>
            </div>
        </div>
        <div class="flex justify-between items-center mt-8">
            <div id="autosave-status" class="text-xs text-gray-400"></div>
            <div>
                @if($isViewMode)
                    <a href="{{ route('inspector.reports.download', $report->id) }}" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold px-6 py-2 rounded">Download Report (PDF)</a>
                @elseif($isEditMode)
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded">Update Report</button>
                @else
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded">Complete Inspection</button>
                @endif
            </div>
        </div>
    </form>

@push('scripts')
<script>
    const form = document.getElementById('inspection-report-form');
    const reportId = {{ $report->id }};
    let autosaveTimeout = null;
    let lastSavedData = null;
    function getFormData() {
        const data = {};
        form.querySelectorAll('textarea').forEach(el => {
            data[el.name] = el.value;
        });
        return data;
    }
    function autoSave() {
        const data = getFormData();
        if (JSON.stringify(data) === JSON.stringify(lastSavedData)) return;
        lastSavedData = data;
        document.getElementById('autosave-status').textContent = 'Saving...';
        fetch("{{ route('inspector.reports.autosave', $report->id) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
            },
            body: JSON.stringify({ data, progress: 80 })
        }).then(res => res.json()).then(res => {
            if (res.success) {
                document.getElementById('autosave-status').textContent = 'All changes saved';
            } else {
                document.getElementById('autosave-status').textContent = 'Auto-save failed';
            }
        }).catch(() => {
            document.getElementById('autosave-status').textContent = 'Auto-save failed';
        });
    }
    form.querySelectorAll('textarea').forEach(el => {
        el.addEventListener('input', function() {
            clearTimeout(autosaveTimeout);
            autosaveTimeout = setTimeout(autoSave, 1000);
        });
    });
    form.addEventListener('submit', function(e) {
        document.getElementById('report-data').value = JSON.stringify(getFormData());
    });
</script>
@endpush
@endsection 