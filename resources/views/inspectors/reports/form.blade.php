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
    <form id="inspection-report-form" method="POST" action="{{ route('inspector.reports.complete', $report->id) }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="data" id="report-data" />
        <div class="space-y-6">
            @foreach($services as $service)
            <div class="bg-white border rounded-lg p-4">
                <h2 class="font-semibold text-lg mb-2">{{ $service->name }} <span class="text-xs text-gray-400">({{ $service->getCategoryDisplayName() }})</span></h2>
                <div class="mb-2 text-sm text-gray-500">{{ $service->description }}</div>
                <textarea class="w-full border rounded px-3 py-2" rows="3" name="service_notes_{{ $service->id }}" placeholder="Enter findings, notes, or results...">{{ $report->data['service_notes_'.$service->id] ?? '' }}</textarea>
                <div class="mt-2">
                    <label for="service_photo_{{ $service->id }}" class="text-sm font-medium text-gray-700">Take or Upload Photo (Optional)</label>
                    <input type="file" accept="image/*" capture="environment" name="service_photo_{{ $service->id }}" id="service_photo_{{ $service->id }}" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"/>
                </div>
            </div>
            @endforeach
            <div class="bg-white border rounded-lg p-4">
                <label class="block font-semibold mb-2">General Comments / Summary</label>
                <textarea class="w-full border rounded px-3 py-2" rows="4" name="general_comments" placeholder="Summary, recommendations, or additional notes...">{{ $report->data['general_comments'] ?? '' }}</textarea>
            </div>
        </div>
        <div class="flex justify-between items-center mt-8">
            <div id="autosave-status" class="text-xs text-gray-400"></div>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded">Complete Inspection</button>
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