<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\Plan;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Subscription::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "user_id"           =>  function () {
                return User::factory()->create();
            },
            "plan_id"           =>  function () {
                return Plan::factory()->create();
            },
            "start_date"        =>  Carbon::now(),
            "expiration_date"   =>  Carbon::now()->addMinutes(
                config("constants.subscriptions.minutes_for_expiration")
            ),
            "is_active"         =>  true
        ];
    }

    /**
     * Devuelve una suscripcion inactiva
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function active()
    {
        return $this->state(function () {
            return [
                "is_active"         =>  true
            ];
        });
    }

    /**
     * Devuelve una suscripcion inactiva
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function inactive()
    {
        return $this->state(function () {
            return [
                "is_active"         =>  false
            ];
        });
    }

    public function activeOrInactive()
    {
        return $this->state(function () {
            return [
                "is_active"         =>  $this->faker->boolean()
            ];
        });
    }
}
