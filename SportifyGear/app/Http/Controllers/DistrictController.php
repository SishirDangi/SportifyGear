<?php

namespace App\Http\Controllers;

use App\Models\District;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    /**
     * Get districts by province that have at least one active shipping zone.
     *
     * @param  int  $province
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function byProvince($province, Request $request)
    {
        $districts = District::where('province_id', $province)
            ->whereHas('shippingZones', function ($query) {
                $query->where('is_active', true);
            })
            ->orderBy('name')
            ->get();

        return response()->json($districts);
    }
}
