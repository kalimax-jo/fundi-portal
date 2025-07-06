@extends('layouts.business-partner')

@section('content')
    <div class="py-12">
        <div class="max-w-2xl mx-auto bg-white p-8 rounded shadow">
            <h1 class="text-2xl font-bold mb-4">Test Partner Dashboard</h1>
            <p class="mb-2">This is a clean test view for the business partner dashboard.</p>
            @if(auth()->check())
                <p>Welcome, {{ auth()->user()->name }} ({{ auth()->user()->email }})!</p>
            @else
                <p>No user is currently authenticated.</p>
            @endif
        </div>
    </div>
@endsection 