<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShippingChargeSetting;
use Flash;

class ShippingChargeController extends Controller
{
    /**
     * Display the shipping charge settings page
     */
    public function index()
    {
        $settings = ShippingChargeSetting::getSettings();
        return view('backend.setup_configurations.shipping_charge.index', compact('settings'));
    }

    /**
     * Update shipping charge settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'is_enabled' => 'boolean',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'inside_dhaka_charge' => 'required|numeric|min:0',
            'outside_dhaka_charge' => 'required|numeric|min:0',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
            'delivery_time_inside' => 'nullable|string|max:255',
            'delivery_time_outside' => 'nullable|string|max:255',
        ]);

        $settings = ShippingChargeSetting::first();
        if (!$settings) {
            $settings = new ShippingChargeSetting();
        }

        $settings->fill([
            'is_enabled' => $request->has('is_enabled'),
            'title' => $request->title,
            'description' => $request->description,
            'inside_dhaka_charge' => $request->inside_dhaka_charge,
            'outside_dhaka_charge' => $request->outside_dhaka_charge,
            'free_shipping_threshold' => $request->free_shipping_threshold,
            'delivery_time_inside' => $request->delivery_time_inside,
            'delivery_time_outside' => $request->delivery_time_outside,
        ]);

        $settings->save();

        flash(translate('Shipping charge settings updated successfully'))->success();
        return redirect()->route('shipping_charge.index');
    }
}
