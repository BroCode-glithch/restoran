@extends('layouts.app')

@section('title', 'Our Team | ' . getSetting('site_title'))

@section('content')
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h5 class="section-title ff-secondary text-center text-primary fw-normal">Team Members</h5>
            <h1 class="mb-3">Meet the people running the kitchen and operations.</h1>
            <p class="text-muted mb-0">This page is powered by the current workspace users so the public site always stays current.</p>
        </div>

        <div class="row g-4">
            @forelse($teamMembers as $member)
                @php
                    $parts = preg_split('/\s+/', trim($member->name));
                    $initials = strtoupper(substr($parts[0] ?? 'T', 0, 1) . substr($parts[1] ?? '', 0, 1));
                @endphp
                <div class="col-lg-3 col-md-6">
                    <div class="team-item text-center rounded overflow-hidden h-100 cursor-pointer">
                        <div class="rounded-circle overflow-hidden m-4 d-flex align-items-center justify-content-center mx-auto" style="width:140px;height:140px;background:linear-gradient(135deg, rgba(254,161,22,.18), rgba(15,23,43,.08));">
                            <span class="display-6 fw-bold text-dark">{{ $initials }}</span>
                        </div>
                        <h5 class="mb-0">{{ $member->name }}</h5>
                        <small class="text-muted d-block mb-2">{{ roleLabel($member->role) }}</small>
                        <div class="px-3 pb-4">
                            @if($member->phone)
                                <div class="small text-muted mb-1"><i class="fa-solid fa-phone me-1"></i>{{ $member->phone }}</div>
                            @endif
                            @if($member->email)
                                <div class="small text-muted text-truncate"><i class="fa-solid fa-envelope me-1"></i>{{ $member->email }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-light border">
                        No active team members have been assigned yet.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
