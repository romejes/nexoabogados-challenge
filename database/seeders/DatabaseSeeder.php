<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $plans = Plan::factory()->count(2)->create();

        for ($i=0; $i < 20; $i++) {
            $user = User::factory()->create();

            $randomPlan = rand($plans[0]->id, $plans[1]->id);

            $subscription = Subscription::factory()->activeOrInactive()->create([
                "user_id"   =>  $user->id,
                "plan_id"   =>  $randomPlan
            ]);

            $overridePayment = [
                "subscription_id"   =>  $subscription->id
            ];

            if ($subscription->is_active) {
                Payment::factory()->create($overridePayment);
            } else{
                Payment::factory()->isNotPaid()->create($overridePayment);
            }
        }
    }
}
