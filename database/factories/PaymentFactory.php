<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'subscription_id'   =>  function () {
                return Subscription::factory()->create();
            },
            "is_paid"           =>  true,
            "payment_date"      =>  Carbon::now(),
            "attempts"          =>  null
        ];
    }

    /**
     * Devuelve una suscripcion inactiva
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function isNotPaid()
    {
        return $this->state(function () {
            return [
                "is_paid"           =>  false,
                "payment_date"      =>  null
            ];
        });
    }
}
