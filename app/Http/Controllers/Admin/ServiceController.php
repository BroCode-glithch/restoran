<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $businessId = currentBusinessId();
        $perPage = $this->perPage($request);

        return view('admin.services.index', [
            'services' => Service::query()
                ->where('business_id', $businessId)
                ->latest()
                ->paginate($perPage)
                ->withQueryString(),
            'editingService' => null,
            'perPage' => $perPage,
        ]);
    }

    public function edit(Request $request, Service $service)
    {
        $this->authorizeService($service);

        $businessId = currentBusinessId();
        $perPage = $this->perPage($request);

        return view('admin.services.index', [
            'services' => Service::query()
                ->where('business_id', $businessId)
                ->latest()
                ->paginate($perPage)
                ->withQueryString(),
            'editingService' => $service,
            'perPage' => $perPage,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatePayload($request);

        Service::create($data);

        SystemLog::create([
            'business_id' => currentBusinessId(),
            'actor_user_id' => auth()->id(),
            'level' => 'info',
            'category' => 'services',
            'message' => 'Service created: ' . $data['title'],
        ]);

        toastr()->success('Service created.', 'Saved', ['timeOut' => 3000]);

        return redirect()->route('admin.services.index');
    }

    public function update(Request $request, Service $service)
    {
        $this->authorizeService($service);

        $data = $this->validatePayload($request);
        $service->update($data);

        SystemLog::create([
            'business_id' => currentBusinessId(),
            'actor_user_id' => auth()->id(),
            'level' => 'info',
            'category' => 'services',
            'message' => 'Service updated: ' . $service->title,
        ]);

        toastr()->success('Service updated.', 'Saved', ['timeOut' => 3000]);

        return redirect()->route('admin.services.index');
    }

    public function destroy(Service $service)
    {
        $this->authorizeService($service);

        $title = $service->title;
        $service->delete();

        SystemLog::create([
            'business_id' => currentBusinessId(),
            'actor_user_id' => auth()->id(),
            'level' => 'warning',
            'category' => 'services',
            'message' => 'Service deleted: ' . $title,
        ]);

        toastr()->success('Service deleted.', 'Removed', ['timeOut' => 3000]);

        return redirect()->route('admin.services.index');
    }

    protected function validatePayload(Request $request)
    {
        return $request->validate([
            'icon' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    protected function perPage(Request $request)
    {
        $allowed = [5, 10, 20, 50];
        $perPage = (int) $request->query('per_page', 10);

        return in_array($perPage, $allowed, true) ? $perPage : 10;
    }

    protected function authorizeService(Service $service)
    {
        if ($service->business_id !== currentBusinessId()) {
            abort(404);
        }
    }
}
