<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use App\Models\Settings;
use App\Services\SettingsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index(SettingsRepository $settingsRepository)
    {
        $businessId = currentBusinessId();
        $settings = $settingsRepository->all($businessId);

        return view('admin.settings.index', [
            'settings' => $settings,
            'businessId' => $businessId,
        ]);
    }

    public function update(Request $request, SettingsRepository $settingsRepository)
    {
        $businessId = currentBusinessId();
        $payload = $request->input('settings', []);

        if ($request->hasFile('branding_logo_file')) {
            $payload['branding.logo_url'] = $request->file('branding_logo_file')->store('branding', 'public');
        }

        if ($request->hasFile('branding_favicon_file')) {
            $payload['branding.favicon_url'] = $request->file('branding_favicon_file')->store('branding', 'public');
        }

        foreach ($payload as $key => $value) {
            $settingsRepository->put($key, $value, $businessId, $this->sectionForKey($key));
        }

        SystemLog::create([
            'business_id' => $businessId,
            'actor_user_id' => auth()->id(),
            'level' => 'info',
            'category' => 'settings',
            'message' => 'Business settings updated.',
        ]);

        toastr()->success('Settings updated successfully.', 'Saved', ['timeOut' => 3000]);

        return redirect()->route('admin.settings.index');
    }

    protected function sectionForKey($key)
    {
        if (strpos($key, 'branding.') === 0) {
            return 'branding';
        }

        if (strpos($key, 'contact.') === 0) {
            return 'contact';
        }

        if (strpos($key, 'operations.') === 0) {
            return 'operations';
        }

        if (strpos($key, 'notifications.') === 0) {
            return 'notifications';
        }

        if (strpos($key, 'integrations.') === 0) {
            return 'integrations';
        }

        return 'general';
    }
}
