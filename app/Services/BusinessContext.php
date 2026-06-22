<?php

namespace App\Services;

use App\Models\Business;
use Illuminate\Support\Facades\Schema;

class BusinessContext
{
    public function current()
    {
        try {
            if (!Schema::hasTable('businesses')) {
                return null;
            }
        } catch (\Throwable $e) {
            return null;
        }

        $businessId = session('business_id');

        if ($businessId) {
            $business = Business::query()->find($businessId);

            if ($business) {
                return $business;
            }
        }

        $defaultBusiness = Business::query()
            ->where('is_default', true)
            ->where('status', 'active')
            ->first();

        if ($defaultBusiness) {
            return $defaultBusiness;
        }

        return Business::query()->where('status', 'active')->orderBy('id')->first();
    }

    public function currentId()
    {
        $business = $this->current();

        return $business ? $business->id : null;
    }

    public function setCurrent($businessId)
    {
        session(['business_id' => $businessId]);
    }

    public function clear()
    {
        session()->forget('business_id');
    }
}
