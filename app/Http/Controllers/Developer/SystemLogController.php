<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class SystemLogController extends Controller
{
    public function index(Request $request)
    {
        $query = SystemLog::query()->where('business_id', currentBusinessId())->latest();

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('level')) {
            $query->where('level', $request->input('level'));
        }

        return view('developer.logs.index', [
            'logs' => $query->paginate(20)->withQueryString(),
        ]);
    }
}
