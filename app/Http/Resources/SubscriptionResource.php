<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
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
            "id"                =>  $this->id,
            "start_date"        =>  $this->start_date,
            "expiration_date"   =>  $this->expiration_date,
            "is_active"         =>  $this->is_active,
            "user"              =>  $this->user,
            "plan"              =>  $this->plan
        ];
    }
}
