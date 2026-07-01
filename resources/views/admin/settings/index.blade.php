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

<style>
    .settings-color-field .input-group-text {
        background: rgba(254, 161, 22, 0.12);
        border-color: rgba(15, 23, 43, 0.1);
    }

    .settings-color-swatch {
        width: 42px;
        min-width: 42px;
        border: 0;
        border-radius: 14px;
        padding: 0;
        overflow: hidden;
        position: relative;
        cursor: pointer;
        background: transparent;
    }

    .settings-color-swatch::-webkit-color-swatch-wrapper {
        padding: 0;
    }

    .settings-color-swatch::-webkit-color-swatch {
        border: 0;
        border-radius: 14px;
    }

    .settings-dropzone {
        border: 1px dashed rgba(15, 23, 43, 0.18);
        background: linear-gradient(180deg, rgba(248, 250, 252, 0.95), rgba(255, 255, 255, 0.92));
        border-radius: 22px;
        padding: 1rem;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
    }

    .settings-dropzone.is-dragover {
        border-color: var(--ops-primary);
        box-shadow: 0 16px 36px rgba(254, 161, 22, 0.12);
        transform: translateY(-1px);
    }

    .settings-dropzone-preview {
        width: 100%;
        min-height: 180px;
        border-radius: 18px;
        object-fit: cover;
        background: rgba(15, 23, 43, 0.04);
    }

    .settings-dropzone-file {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
    }

    .settings-integration-keys {
        background: rgba(15, 23, 43, 0.02);
        border: 1px solid rgba(15, 23, 43, 0.08);
        border-radius: 22px;
        padding: 1rem;
    }
</style>

<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="vstack gap-4">
    @csrf
    @method('PUT')

    <div class="ops-setting-card p-4 mb-4">
        <h4 class="mb-3">Branding</h4>
        <div class="row g-3">
            <div class="col-md-6"><input type="text" name="settings[branding.business_name]" class="form-control" placeholder="Business name" value="{{ old('settings.branding.business_name', $settings['branding.business_name'] ?? '') }}"></div>
            @php
                $primaryColor = old('settings.branding.primary_color', $settings['branding.primary_color'] ?? '#FEA116');
                $secondaryColor = old('settings.branding.secondary_color', $settings['branding.secondary_color'] ?? '#0F172B');
            @endphp
            <div class="col-md-3">
                <div class="input-group settings-color-field">
                    <span class="input-group-text">Primary</span>
                    <input type="color" class="form-control settings-color-swatch" data-color-target="primaryColorText" value="#FEA116" aria-label="Primary color picker">
                    <input type="text" id="primaryColorText" name="settings[branding.primary_color]" class="form-control" placeholder="Hex, RGB or RGBA" value="{{ $primaryColor }}" data-color-preview="#primaryColorPreview">
                </div>
                <div id="primaryColorPreview" class="small text-muted mt-2">Preview</div>
            </div>
            <div class="col-md-3">
                <div class="input-group settings-color-field">
                    <span class="input-group-text">Secondary</span>
                    <input type="color" class="form-control settings-color-swatch" data-color-target="secondaryColorText" value="#0F172B" aria-label="Secondary color picker">
                    <input type="text" id="secondaryColorText" name="settings[branding.secondary_color]" class="form-control" placeholder="Hex, RGB or RGBA" value="{{ $secondaryColor }}" data-color-preview="#secondaryColorPreview">
                </div>
                <div id="secondaryColorPreview" class="small text-muted mt-2">Preview</div>
            </div>
            <div class="col-md-6"><input type="text" name="settings[branding.font_family]" class="form-control" placeholder="Font family" value="{{ old('settings.branding.font_family', $settings['branding.font_family'] ?? '"Nunito", sans-serif') }}"></div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Logo upload</label>
                <div class="settings-dropzone position-relative" data-dropzone="brandingLogo">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img id="brandingLogoPreview" src="{{ !empty($settings['branding.logo_url'] ?? null) ? mediaUrl($settings['branding.logo_url']) : asset('assets/img/hero.png') }}" alt="Logo preview" class="settings-dropzone-preview" style="max-width: 180px; width: 180px; height: 180px;">
                        <div>
                            <div class="fw-bold mb-1">Drag and drop logo</div>
                            <div class="text-muted small">Or click to choose a file. PNG, JPG or SVG.</div>
                            <div class="small text-muted mt-2" id="brandingLogoName">{{ !empty($settings['branding.logo_url'] ?? null) ? basename($settings['branding.logo_url']) : 'No file selected' }}</div>
                        </div>
                    </div>
                    <input type="file" name="branding_logo_file" id="brandingLogoInput" accept="image/*" class="settings-dropzone-file">
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Favicon upload</label>
                <div class="settings-dropzone position-relative" data-dropzone="brandingFavicon">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img id="brandingFaviconPreview" src="{{ !empty($settings['branding.favicon_url'] ?? null) ? mediaUrl($settings['branding.favicon_url']) : asset('favicon.ico') }}" alt="Favicon preview" class="settings-dropzone-preview" style="max-width: 96px; width: 96px; height: 96px;">
                        <div>
                            <div class="fw-bold mb-1">Drag and drop favicon</div>
                            <div class="text-muted small">Or click to choose a file. PNG or ICO.</div>
                            <div class="small text-muted mt-2" id="brandingFaviconName">{{ !empty($settings['branding.favicon_url'] ?? null) ? basename($settings['branding.favicon_url']) : 'No file selected' }}</div>
                        </div>
                    </div>
                    <input type="file" name="branding_favicon_file" id="brandingFaviconInput" accept="image/*,.ico" class="settings-dropzone-file">
                </div>
            </div>
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
            <div class="col-md-4"><input type="number" step="0.01" name="settings[operations.delivery_fee_inside_school]" class="form-control" placeholder="Delivery fee inside school" value="{{ old('settings.operations.delivery_fee_inside_school', $settings['operations.delivery_fee_inside_school'] ?? ($settings['operations.delivery_fee'] ?? '0')) }}"></div>
            <div class="col-md-4"><input type="number" step="0.01" name="settings[operations.delivery_fee_outside_school]" class="form-control" placeholder="Delivery fee outside school" value="{{ old('settings.operations.delivery_fee_outside_school', $settings['operations.delivery_fee_outside_school'] ?? $settings['operations.delivery_fee_inside_school'] ?? ($settings['operations.delivery_fee'] ?? '0')) }}"></div>
            <div class="col-md-4"><input type="text" name="settings[operations.business_hours]" class="form-control" placeholder="Business hours" value="{{ old('settings.operations.business_hours', $settings['operations.business_hours'] ?? '') }}"></div>
            <div class="col-12">
                <div class="alert alert-info mb-0">Set the lower fee for school premises delivery and the higher fee for addresses outside the school. Managers or admins can adjust these values here.</div>
            </div>
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
            <div class="col-md-3 form-check ps-5">
                <input type="hidden" name="settings[integrations.korapay_enabled]" value="0">
                <input class="form-check-input" type="checkbox" name="settings[integrations.korapay_enabled]" value="1" id="korapay_enabled" {{ ($settings['integrations.korapay_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
                <label class="form-check-label" for="korapay_enabled">Korapay</label>
            </div>
        </div>

        @if(auth()->user() && auth()->user()->isDeveloper())
            <div class="settings-integration-keys mt-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <div>
                        <h5 class="mb-1">Developer credentials</h5>
                        <p class="text-muted mb-0">Add the keys here. Admins can only toggle providers on or off.</p>
                    </div>
                    <span class="badge bg-dark text-white">Developer only</span>
                </div>
                <div class="row g-3">
                    <div class="col-md-6"><input type="text" name="settings[integrations.stripe_public_key]" class="form-control" placeholder="Stripe public key" value="{{ old('settings.integrations.stripe_public_key', $settings['integrations.stripe_public_key'] ?? '') }}"></div>
                    <div class="col-md-6"><input type="password" name="settings[integrations.stripe_secret_key]" class="form-control" placeholder="Stripe secret key" value="{{ old('settings.integrations.stripe_secret_key', $settings['integrations.stripe_secret_key'] ?? '') }}"></div>
                    <div class="col-md-6"><input type="text" name="settings[integrations.paystack_public_key]" class="form-control" placeholder="Paystack public key" value="{{ old('settings.integrations.paystack_public_key', $settings['integrations.paystack_public_key'] ?? '') }}"></div>
                    <div class="col-md-6"><input type="password" name="settings[integrations.paystack_secret_key]" class="form-control" placeholder="Paystack secret key" value="{{ old('settings.integrations.paystack_secret_key', $settings['integrations.paystack_secret_key'] ?? '') }}"></div>
                    <div class="col-md-6"><input type="password" name="settings[integrations.resend_api_key]" class="form-control" placeholder="Resend API key" value="{{ old('settings.integrations.resend_api_key', $settings['integrations.resend_api_key'] ?? '') }}"></div>
                    <div class="col-md-6"><input type="text" name="settings[integrations.twilio_account_sid]" class="form-control" placeholder="Twilio account SID" value="{{ old('settings.integrations.twilio_account_sid', $settings['integrations.twilio_account_sid'] ?? '') }}"></div>
                    <div class="col-md-6"><input type="password" name="settings[integrations.twilio_auth_token]" class="form-control" placeholder="Twilio auth token" value="{{ old('settings.integrations.twilio_auth_token', $settings['integrations.twilio_auth_token'] ?? '') }}"></div>
                    <div class="col-md-6"><input type="text" name="settings[integrations.twilio_phone_number]" class="form-control" placeholder="Twilio phone number" value="{{ old('settings.integrations.twilio_phone_number', $settings['integrations.twilio_phone_number'] ?? '') }}"></div>
                    <div class="col-md-6"><input type="text" name="settings[integrations.korapay_public_key]" class="form-control" placeholder="Korapay public key" value="{{ old('settings.integrations.korapay_public_key', $settings['integrations.korapay_public_key'] ?? '') }}"></div>
                    <div class="col-md-6"><input type="password" name="settings[integrations.korapay_secret_key]" class="form-control" placeholder="Korapay secret key" value="{{ old('settings.integrations.korapay_secret_key', $settings['integrations.korapay_secret_key'] ?? '') }}"></div>
                    <div class="col-md-6"><input type="text" name="settings[integrations.korapay_base_url]" class="form-control" placeholder="Korapay checkout URL" value="{{ old('settings.integrations.korapay_base_url', $settings['integrations.korapay_base_url'] ?? 'https://checkout.korapay.com') }}"></div>
                </div>
            </div>
        @endif
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

@push('scripts')
<script>
    (function () {
        function isValidColor(value) {
            var test = document.createElement('div');
            test.style.color = '';
            test.style.color = value;
            return test.style.color !== '';
        }

        function updateColorField(textInput, swatchInput, previewSelector) {
            if (!textInput || !swatchInput) {
                return;
            }

            var preview = previewSelector ? document.querySelector(previewSelector) : null;

            function apply(value) {
                if (!value) {
                    return;
                }

                if (isValidColor(value)) {
                    if (/^rgba?\(/i.test(value.trim()) || /^#([0-9a-f]{3}|[0-9a-f]{6})$/i.test(value.trim())) {
                        swatchInput.style.backgroundColor = value;
                    }
                    if (/^#([0-9a-f]{3}|[0-9a-f]{6})$/i.test(value.trim())) {
                        swatchInput.value = value;
                    }
                    if (preview) {
                        preview.style.color = value;
                        preview.textContent = 'Preview: ' + value;
                    }
                }
            }

            apply(textInput.value);

            textInput.addEventListener('input', function () {
                apply(textInput.value);
            });

            swatchInput.addEventListener('input', function () {
                textInput.value = swatchInput.value;
                apply(textInput.value);
            });
        }

        function bindDropzone(dropzoneSelector, inputSelector, previewSelector, nameSelector) {
            var dropzone = document.querySelector(dropzoneSelector);
            var input = document.querySelector(inputSelector);
            var preview = document.querySelector(previewSelector);
            var nameNode = document.querySelector(nameSelector);

            if (!dropzone || !input || !preview) {
                return;
            }

            var activate = function (file) {
                if (!file) {
                    return;
                }

                if (file.type && file.type.indexOf('image/') !== 0 && file.type !== 'image/x-icon') {
                    return;
                }

                input.files = new DataTransfer().files;
                var transfer = new DataTransfer();
                transfer.items.add(file);
                input.files = transfer.files;

                var reader = new FileReader();
                reader.onload = function (event) {
                    preview.src = event.target.result;
                };
                reader.readAsDataURL(file);

                if (nameNode) {
                    nameNode.textContent = file.name;
                }
            };

            dropzone.addEventListener('dragover', function (event) {
                event.preventDefault();
                dropzone.classList.add('is-dragover');
            });

            dropzone.addEventListener('dragleave', function () {
                dropzone.classList.remove('is-dragover');
            });

            dropzone.addEventListener('drop', function (event) {
                event.preventDefault();
                dropzone.classList.remove('is-dragover');
                activate(event.dataTransfer.files[0]);
            });

            input.addEventListener('change', function () {
                activate(input.files[0]);
            });
        }

        updateColorField(
            document.getElementById('primaryColorText'),
            document.querySelector('[data-color-target="primaryColorText"]'),
            '#primaryColorPreview'
        );
        updateColorField(
            document.getElementById('secondaryColorText'),
            document.querySelector('[data-color-target="secondaryColorText"]'),
            '#secondaryColorPreview'
        );

        bindDropzone('[data-dropzone="brandingLogo"]', '#brandingLogoInput', '#brandingLogoPreview', '#brandingLogoName');
        bindDropzone('[data-dropzone="brandingFavicon"]', '#brandingFaviconInput', '#brandingFaviconPreview', '#brandingFaviconName');
    }());
</script>
@endpush
@endsection
