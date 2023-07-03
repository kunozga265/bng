<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlotResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'            => intval($this->id),
            'status'        => intval($this->status),
            'name'          => $this->name,
            'hectare'       => floatval($this->hectare),
            'site'          => new SiteResource($this->site),
            'user'          => new UserResource($this->user),
        ];
    }
}
