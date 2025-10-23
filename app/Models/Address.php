<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class Address extends Model
{
    use PreventDemoModeChanges;

    // Mohammad Hassan - Expand fillable to support mass assignment safely
    // Note: 'city' column has been removed from database via migration
    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'address',
        'country_id',
        'state_id',
        'city_id',
        'area_id',
        'longitude',
        'latitude',
        'postal_code',
        'set_default',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

     public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
