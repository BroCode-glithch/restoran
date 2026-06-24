@extends('layouts.app')

@section('title', 'Booking | ' . getSetting('site_title'))

@section('content')
@php
    $whatsappPhone = preg_replace('/[^0-9]/', '', (string) $whatsappNumber);
@endphp

<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-4 align-items-stretch">
            <div class="col-lg-5">
                <div class="booking-card h-100 p-4 p-lg-5 rounded-4 bg-white border shadow-sm">
                    <h5 class="section-title ff-secondary text-start text-primary fw-normal">Reservation</h5>
                    <h1 class="mb-3">Book a table or catering slot in one WhatsApp tap.</h1>
                    <p class="text-muted mb-4">This booking page is driven by runtime settings so staff can update hours, contact numbers and service details without a code change.</p>

                    <div class="d-grid gap-3 mb-4">
                        <div class="p-3 rounded-4 bg-light border">
                            <div class="small text-muted mb-1">Business hours</div>
                            <div class="fw-semibold">{{ $businessHours ?: 'Not configured yet' }}</div>
                        </div>
                        <div class="p-3 rounded-4 bg-light border">
                            <div class="small text-muted mb-1">Delivery fee</div>
                            <div class="fw-semibold">{{ moneyFormat((float) $deliveryFee) }}</div>
                        </div>
                        <div class="p-3 rounded-4 bg-light border">
                            <div class="small text-muted mb-1">WhatsApp contact</div>
                            <div class="fw-semibold">{{ $whatsappNumber ?: 'Not configured yet' }}</div>
                        </div>
                    </div>

                    @if(!empty($services) && $services->count())
                        <div class="mb-3 fw-semibold">Popular service options</div>
                        <div class="vstack gap-3">
                            @foreach($services as $service)
                                <div class="service-item rounded-4 p-3 cursor-pointer">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="ops-nav-icon">
                                            <i class="{{ $service->icon }}"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $service->title }}</div>
                                            <div class="small text-muted">{{ $service->description }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-7">
                <div class="booking-card h-100 p-4 p-lg-5 rounded-4 bg-dark text-white shadow">
                    <h5 class="section-title ff-secondary text-start text-primary fw-normal">Reservation Form</h5>
                    <h2 class="mb-4">Send a prefilled booking request on WhatsApp.</h2>

                    <form id="bookingForm" class="row g-3" data-whatsapp-phone="{{ $whatsappPhone }}">
                        <div class="col-md-6">
                            <label class="form-label">Your name</label>
                            <input type="text" class="form-control" name="name" placeholder="Full name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">WhatsApp number</label>
                            <input type="tel" class="form-control" name="phone" placeholder="+234..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Booking date</label>
                            <input type="date" class="form-control" name="date" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Preferred time</label>
                            <input type="time" class="form-control" name="time" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Guests</label>
                            <select class="form-select" name="guests" required>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'guest' : 'guests' }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" rows="4" placeholder="Special request, catering details, seating preference..."></textarea>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary w-100 py-3" type="submit">
                                <i class="fa-brands fa-whatsapp me-1"></i>
                                Reserve on WhatsApp
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        var form = document.getElementById('bookingForm');
        if (!form) {
            return;
        }

        form.addEventListener('submit', function (event) {
            event.preventDefault();

            var phone = (form.dataset.whatsappPhone || '').replace(/[^0-9]/g, '');
            if (!phone) {
                return;
            }

            var data = new FormData(form);
            var message = [
                'Hello, I would like to make a booking.',
                'Name: ' + (data.get('name') || ''),
                'WhatsApp: ' + (data.get('phone') || ''),
                'Date: ' + (data.get('date') || ''),
                'Time: ' + (data.get('time') || ''),
                'Guests: ' + (data.get('guests') || ''),
                'Notes: ' + (data.get('notes') || 'None')
            ].join('\n');

            window.open('https://wa.me/' + phone + '?text=' + encodeURIComponent(message), '_blank', 'noopener');
        });
    }());
</script>
@endpush
@endsection
