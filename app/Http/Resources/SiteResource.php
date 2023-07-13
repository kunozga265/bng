<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class SiteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $available_plots = $this->plots()->where('status',1)->count();
        $negotiating_plots = $this->plots()->where('status',2)->count();
        $sold_plots = $this->plots()->where('status',3)->count();
        $all_plots = $this->plots()->count();
        $status = $sold_plots > 0 ? 1 : 0;
        $now = Carbon::now()->getTimestamp();
        $bookings = $this->bookings()->where('from','>=', $now)->get();

        return [
            'id'                => intval($this->id),
            'name'              => $this->name,
            'plot_width'        => floatval($this->plot_width),
            'plot_height'       => floatval($this->plot_height),
            'plot_price'        => floatval($this->plot_price),
            'location'          => $this->location,
            'district'          => $this->district,
            'layout'            => $this->layout,
            'status'            => intval($status),
            'available_plots'   => intval($available_plots),
            'negotiating_plots' => intval($negotiating_plots),
            'sold_plots'        => intval($sold_plots),
            'all_plots'         => intval($all_plots),
            'totalBookingsCount'=> intval($this->bookings()->count()),
        ];
    }
}
