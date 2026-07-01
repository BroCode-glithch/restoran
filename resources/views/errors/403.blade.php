@extends('layouts.app')

@section('title', 'Access Denied')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="display-1 text-danger">403</div>
                    <h1 class="h3 mb-3">Access Denied</h1>
                    <p class="text-muted mb-4">You do not have permission to view this page.</p>
                    <a href="{{ url()->previous() ?: route('dashboard') }}" class="btn btn-primary me-2">Go Back</a>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
