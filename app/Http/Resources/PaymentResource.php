<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            "id"            =>  $this->id,
            "subscription"  =>  new SubscriptionResource($this->subscription),
            "is_paid"       =>  $this->is_paid,
            "payment_date"  =>  $this->payment_date,
            "attempts"      =>  $this->attempts
        ];
    }
}
