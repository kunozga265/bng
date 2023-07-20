<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    public function plots()
    {
        return $this->hasMany(Plot::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    protected $fillable=[
        'name',
        'plot_width',
        'plot_height',
        'plot_price',
        'layout',
        'location',
        'district',
    ];
}
