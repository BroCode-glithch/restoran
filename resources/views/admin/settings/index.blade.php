@extends('layouts.dashboard')

@section('title', 'Settings | ' . getSetting('branding.business_name', getSetting('site_title', config('app.name'))))

@section('content')
<div class="ops-hero mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-lg-8">
            <div class="badge bg-white text-dark px-3 py-2 rounded-pill mb-3">Runtime Settings</div>
            <h1 class="fw-bold mb-2">Edit branding, business and integration settings.</h1>
            <p class="mb-0 text-white-50">All values are stored in the database so the platform can be rebranded without code changes.</p>
        </div>
    </div>
</div>

<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="vstack gap-4">
    @csrf
    @method('PUT')

    <div class="ops-setting-card p-4 mb-4">
        <h4 class="mb-3">Branding</h4>
        <div class="row g-3">
            <div class="col-md-6"><input type="text" name="settings[branding.business_name]" class="form-control" placeholder="Business name" value="{{ old('settings.branding.business_name', $settings['branding.business_name'] ?? '') }}"></div>
            <div class="col-md-3"><input type="text" name="settings[branding.primary_color]" class="form-control" placeholder="Primary color" value="{{ old('settings.branding.primary_color', $settings['branding.primary_color'] ?? '#FEA116') }}"></div>
            <div class="col-md-3"><input type="text" name="settings[branding.secondary_color]" class="form-control" placeholder="Secondary color" value="{{ old('settings.branding.secondary_color', $settings['branding.secondary_color'] ?? '#0F172B') }}"></div>
            <div class="col-md-6"><input type="text" name="settings[branding.font_family]" class="form-control" placeholder="Font family" value="{{ old('settings.branding.font_family', $settings['branding.font_family'] ?? '"Nunito", sans-serif') }}"></div>
            <div class="col-md-6"><input type="text" name="settings[branding.logo_url]" class="form-control" placeholder="Logo URL or storage path" value="{{ old('settings.branding.logo_url', $settings['branding.logo_url'] ?? '') }}"></div>
            <div class="col-md-6"><input type="file" name="branding_logo_file" class="form-control"></div>
            <div class="col-md-6"><input type="file" name="branding_favicon_file" class="form-control"></div>
            @if(!empty($settings['branding.logo_url'] ?? null))
                <div class="col-12"><img src="{{ mediaUrl($settings['branding.logo_url']) }}" alt="Logo preview" style="width:96px;height:96px;object-fit:cover;" class="rounded-4 border"></div>
            @endif
        </div>
    </div>

    <div class="ops-setting-card p-4 mb-4">
        <h4 class="mb-3">Contact</h4>
        <div class="row g-3">
            <div class="col-md-6"><input type="email" name="settings[contact.email]" class="form-control" placeholder="Email" value="{{ old('settings.contact.email', $settings['contact.email'] ?? '') }}"></div>
            <div class="col-md-6"><input type="text" name="settings[contact.phone]" class="form-control" placeholder="Phone" value="{{ old('settings.contact.phone', $settings['contact.phone'] ?? '') }}"></div>
            <div class="col-md-6"><input type="text" name="settings[contact.whatsapp_number]" class="form-control" placeholder="WhatsApp number" value="{{ old('settings.contact.whatsapp_number', $settings['contact.whatsapp_number'] ?? '') }}"></div>
            <div class="col-12"><textarea name="settings[contact.address]" class="form-control" rows="3" placeholder="Address">{{ old('settings.contact.address', $settings['contact.address'] ?? '') }}</textarea></div>
        </div>
    </div>

    <div class="ops-setting-card p-4 mb-4">
        <h4 class="mb-3">Operations</h4>
        <div class="row g-3">
            {{-- A currency Div with dropdown of all currencies --}}
            <div class="col-md-4">
                <select name="settings[operations.currency]" class="form-select">

                    @foreach (getCurrencies() as $code => $symbol)
                        <option value="{{ $code }}"
                            {{ ($settings['operations.currency'] ?? 'NGN') === $code ? 'selected' : '' }}>
                            {{ $code }} ({{ $symbol }})
                        </option>
                    @endforeach

                </select>
            </div>
            <div class="col-md-4"><input type="number" step="0.01" name="settings[operations.delivery_fee]" class="form-control" placeholder="Delivery fee" value="{{ old('settings.operations.delivery_fee', $settings['operations.delivery_fee'] ?? '0') }}"></div>
            <div class="col-md-4"><input type="text" name="settings[operations.business_hours]" class="form-control" placeholder="Business hours" value="{{ old('settings.operations.business_hours', $settings['operations.business_hours'] ?? '') }}"></div>
            <div class="col-md-6 form-check ps-5">
                <input type="hidden" name="settings[operations.pickup_enabled]" value="0">
                <input class="form-check-input" type="checkbox" name="settings[operations.pickup_enabled]" value="1" id="pickup_enabled" {{ ($settings['operations.pickup_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
                <label class="form-check-label" for="pickup_enabled">Enable pickup</label>
            </div>
            <div class="col-md-6 form-check ps-5">
                <input type="hidden" name="settings[operations.delivery_enabled]" value="0">
                <input class="form-check-input" type="checkbox" name="settings[operations.delivery_enabled]" value="1" id="delivery_enabled" {{ ($settings['operations.delivery_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
                <label class="form-check-label" for="delivery_enabled">Enable delivery</label>
            </div>
        </div>
    </div>

    <div class="ops-setting-card p-4 mb-4">
        <h4 class="mb-3">Notifications</h4>
        <div class="row g-3">
            <div class="col-md-6 form-check ps-5">
                <input type="hidden" name="settings[notifications.whatsapp_enabled]" value="0">
                <input class="form-check-input" type="checkbox" name="settings[notifications.whatsapp_enabled]" value="1" id="whatsapp_enabled" {{ ($settings['notifications.whatsapp_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
                <label class="form-check-label" for="whatsapp_enabled">Enable WhatsApp quick-chat</label>
            </div>
            <div class="col-md-6 form-check ps-5">
                <input type="hidden" name="settings[notifications.email_enabled]" value="0">
                <input class="form-check-input" type="checkbox" name="settings[notifications.email_enabled]" value="1" id="email_enabled" {{ ($settings['notifications.email_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
                <label class="form-check-label" for="email_enabled">Enable email notifications</label>
            </div>
            <div class="col-12"><textarea name="settings[notifications.whatsapp_template]" class="form-control" rows="3" placeholder="WhatsApp template">{{ old('settings.notifications.whatsapp_template', $settings['notifications.whatsapp_template'] ?? '') }}</textarea></div>
        </div>
    </div>

    <div class="ops-setting-card p-4 mb-4">
        <h4 class="mb-3">Integrations</h4>
        <div class="row g-3">
            <div class="col-md-3 form-check ps-5">
                <input type="hidden" name="settings[integrations.stripe_enabled]" value="0">
                <input class="form-check-input" type="checkbox" name="settings[integrations.stripe_enabled]" value="1" id="stripe_enabled" {{ ($settings['integrations.stripe_enabled'] ?? '0') === '1' ? 'checked' : '' }}>
                <label class="form-check-label" for="stripe_enabled">Stripe</label>
            </div>
            <div class="col-md-3 form-check ps-5">
                <input type="hidden" name="settings[integrations.paystack_enabled]" value="0">
                <input class="form-check-input" type="checkbox" name="settings[integrations.paystack_enabled]" value="1" id="paystack_enabled" {{ ($settings['integrations.paystack_enabled'] ?? '0') === '1' ? 'checked' : '' }}>
                <label class="form-check-label" for="paystack_enabled">Paystack</label>
            </div>
            <div class="col-md-3 form-check ps-5">
                <input type="hidden" name="settings[integrations.resend_enabled]" value="0">
                <input class="form-check-input" type="checkbox" name="settings[integrations.resend_enabled]" value="1" id="resend_enabled" {{ ($settings['integrations.resend_enabled'] ?? '0') === '1' ? 'checked' : '' }}>
                <label class="form-check-label" for="resend_enabled">Resend</label>
            </div>
            <div class="col-md-3 form-check ps-5">
                <input type="hidden" name="settings[integrations.twilio_enabled]" value="0">
                <input class="form-check-input" type="checkbox" name="settings[integrations.twilio_enabled]" value="1" id="twilio_enabled" {{ ($settings['integrations.twilio_enabled'] ?? '0') === '1' ? 'checked' : '' }}>
                <label class="form-check-label" for="twilio_enabled">Twilio</label>
            </div>
        </div>
    </div>

    <div class="ops-setting-card p-4 mb-4">
        <h4 class="mb-3">Footer</h4>
        <div class="row g-3">
            <div class="col-md-6"><input type="text" name="settings[footer.company_title]" class="form-control" placeholder="Footer company title" value="{{ old('settings.footer.company_title', $settings['footer.company_title'] ?? 'Company') }}"></div>
            <div class="col-md-6"><input type="text" name="settings[footer.quick_links_text]" class="form-control" placeholder="Footer quick links text" value="{{ old('settings.footer.quick_links_text', $settings['footer.quick_links_text'] ?? 'Browse the menu, reserve a table, or check the latest testimonials.') }}"></div>
            <div class="col-md-6"><input type="text" name="settings[footer.copyright_text]" class="form-control" placeholder="Copyright text" value="{{ old('settings.footer.copyright_text', $settings['footer.copyright_text'] ?? 'All Rights Reserved.') }}"></div>
            <div class="col-md-6"><input type="text" name="settings[footer.credit_text]" class="form-control" placeholder="Footer credit text" value="{{ old('settings.footer.credit_text', $settings['footer.credit_text'] ?? 'Designed by DailyDew Tech Innovations') }}"></div>
            <div class="col-md-6"><input type="url" name="settings[footer.credit_url]" class="form-control" placeholder="Footer credit URL" value="{{ old('settings.footer.credit_url', $settings['footer.credit_url'] ?? 'https://dailydewtech.com.ng') }}"></div>
            <div class="col-md-6"><input type="url" name="settings[footer.twitter_url]" class="form-control" placeholder="Twitter URL" value="{{ old('settings.footer.twitter_url', $settings['footer.twitter_url'] ?? '') }}"></div>
            <div class="col-md-6"><input type="url" name="settings[footer.facebook_url]" class="form-control" placeholder="Facebook URL" value="{{ old('settings.footer.facebook_url', $settings['footer.facebook_url'] ?? '') }}"></div>
            <div class="col-md-6"><input type="url" name="settings[footer.youtube_url]" class="form-control" placeholder="YouTube URL" value="{{ old('settings.footer.youtube_url', $settings['footer.youtube_url'] ?? '') }}"></div>
            <div class="col-md-6"><input type="url" name="settings[footer.linkedin_url]" class="form-control" placeholder="LinkedIn URL" value="{{ old('settings.footer.linkedin_url', $settings['footer.linkedin_url'] ?? '') }}"></div>
        </div>
    </div>

    <div class="text-end">
        <button type="submit" class="btn btn-primary btn-lg">Save Settings</button>
    </div>
</form>
@endsection
