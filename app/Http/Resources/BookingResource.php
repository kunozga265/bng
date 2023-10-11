<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
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
            'site'          => new SiteResource($this->site),
            'from'          => intval($this->from),
            'to'            => intval($this->to),
            'name'          => $this->name,
            'user'          => $this->user != null ? new UserResource($this->user) : null,
        ];
    }
}
