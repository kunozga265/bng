<?php

namespace App\Http\Resources;

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

        return [
            'id'                => $this->id,
            'plot_width'        => $this->plot_width,
            'plot_height'       => $this->plot_height,
            'plot_price'        => $this->plot_price,
            'location'          => $this->location,
            'district'          => $this->district,
            'layout'            => $this->layout,
            'status'            => $status,
            'available_plots'   => $available_plots,
            'negotiating_plots' => $negotiating_plots,
            'sold_plots'        => $sold_plots,
            'all_plots'         => $all_plots,
        ];
    }
}
