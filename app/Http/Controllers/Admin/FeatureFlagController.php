<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeatureFlag;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class FeatureFlagController extends Controller
{
    public function index()
    {
        return view('admin.flags.index', [
            'flags' => FeatureFlag::query()->where('business_id', currentBusinessId())->latest()->get(),
            'editingFlag' => null,
        ]);
    }

    public function edit(FeatureFlag $flag)
    {
        $this->authorizeFlag($flag);

        return view('admin.flags.index', [
            'flags' => FeatureFlag::query()->where('business_id', currentBusinessId())->latest()->get(),
            'editingFlag' => $flag,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatePayload($request);
        $data['enabled'] = $request->boolean('enabled', false);
        $data['created_by'] = auth()->id();

        FeatureFlag::create($data);

        SystemLog::create([
            'business_id' => currentBusinessId(),
            'actor_user_id' => auth()->id(),
            'level' => 'info',
            'category' => 'flags',
            'message' => 'Feature flag created: ' . $data['key'],
        ]);

        toastr()->success('Feature flag saved.', 'Saved', ['timeOut' => 3000]);

        return redirect()->route('admin.flags.index');
    }

    public function update(Request $request, FeatureFlag $flag)
    {
        $this->authorizeFlag($flag);

        $data = $this->validatePayload($request);
        $data['enabled'] = $request->boolean('enabled', false);

        $flag->update($data);

        SystemLog::create([
            'business_id' => currentBusinessId(),
            'actor_user_id' => auth()->id(),
            'level' => 'info',
            'category' => 'flags',
            'message' => 'Feature flag updated: ' . $flag->key,
        ]);

        toastr()->success('Feature flag updated.', 'Saved', ['timeOut' => 3000]);

        return redirect()->route('admin.flags.index');
    }

    public function destroy(FeatureFlag $flag)
    {
        $this->authorizeFlag($flag);

        $flag->delete();

        SystemLog::create([
            'business_id' => currentBusinessId(),
            'actor_user_id' => auth()->id(),
            'level' => 'warning',
            'category' => 'flags',
            'message' => 'Feature flag deleted: ' . $flag->key,
        ]);

        toastr()->success('Feature flag deleted.', 'Removed', ['timeOut' => 3000]);

        return redirect()->route('admin.flags.index');
    }

    protected function validatePayload(Request $request)
    {
        return $request->validate([
            'key' => ['required', 'string', 'max:255'],
            'label' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);
    }

    protected function authorizeFlag(FeatureFlag $flag)
    {
        if ($flag->business_id !== currentBusinessId()) {
            abort(404);
        }
    }
}
