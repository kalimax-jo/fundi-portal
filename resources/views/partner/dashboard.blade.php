@extends('layouts.business-partner')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Welcome, {{ $user->name }}</h1>
    <h2 class="text-lg mb-2">Partner: <span class="font-semibold">{{ $partner->name }}</span></h2>
    <form id="logout-form" action="{{ route('partner.logout') }}" method="POST" style="display: inline;">
        @csrf
        <button type="submit">Logout</button>
    </form>
    <!-- Add dashboard widgets or stats here -->
@endsection 