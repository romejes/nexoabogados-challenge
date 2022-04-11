<?php

namespace Tests\Feature\CurrentSubscriptionController;

use Tests\TestCase;
use App\Models\Plan;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;

class PutCurrentSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_change_plan()
    {
        $user = User::factory()->create();
        $currentPlan = Plan::factory()->create();
        $newPlan = Plan::factory()->create();
        $currentSubscription = Subscription::factory()->active()->create([
            "user_id"   =>  $user->id,
            "plan_id"   =>  $currentPlan->id
        ]);

        $requestBody = [
            "plan_id"   =>  $newPlan->id
        ];
        $url = sprintf('/api/v1/users/%d/subscriptions/current', $user->id);
        $response = $this->json(Request::METHOD_PUT, $url, $requestBody);

        $response->assertOk();
        $response->assertJsonStructure([
            "id",
            "start_date",
            "expiration_date",
            "is_active",
            "user",
            "plan"
        ]);

        $this->assertDatabaseHas("subscription", [
            "plan_id"   =>  $newPlan->id,
            "user_id"   =>  $user->id,
            "is_active" =>  true
        ]);

        $this->assertDatabaseHas("subscription", [
            "plan_id"   =>  $currentSubscription->plan_id,
            "user_id"   =>  $currentSubscription->user_id,
            "is_active" =>  false
        ]);

        $this->assertDatabaseMissing("subscription", [
            "plan_id"   =>  $currentSubscription->plan_id,
            "user_id"   =>  $currentSubscription->user_id,
            "is_active" =>  true
        ]);
    }

    public function test_change_plan_not_valid_parameters()
    {
        $user = User::factory()->create();
        $requestBody = [
            "plan_id"   =>  0
        ];
        $url = sprintf('/api/v1/users/%d/subscriptions/current', $user->id);
        $response = $this->json(Request::METHOD_PUT, $url, $requestBody);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(["plan_id"]);
    }

    public function test_user_not_found()
    {
        $plan = Plan::factory()->create();
        $requestBody = [
            "plan_id"   =>  $plan->id
        ];

        $url = sprintf('/api/v1/users/%d/subscriptions/current', 1);
        $response = $this->json(Request::METHOD_PUT, $url, $requestBody);

        $response->assertNotFound();
    }

    public function test_cannot_change_to_same_plan()
    {
        $user = User::factory()->create();
        $currentPlan = Plan::factory()->create();
        $currentSubscription = Subscription::factory()->active()->create([
            "user_id"   =>  $user->id,
            "plan_id"   =>  $currentPlan->id
        ]);

        $requestBody = [
            "plan_id"   =>  $currentPlan->id
        ];
        $url = sprintf('/api/v1/users/%d/subscriptions/current', $user->id);
        $response = $this->json(Request::METHOD_PUT, $url, $requestBody);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);

        $this->assertDatabaseHas("subscription", [
            "plan_id"   =>  $currentSubscription->plan_id,
            "user_id"   =>  $currentSubscription->user_id,
            "is_active" =>  true
        ]);

        $this->assertDatabaseMissing("subscription", [
            "plan_id"   =>  $currentSubscription->plan_id,
            "user_id"   =>  $currentSubscription->user_id,
            "is_active" =>  false
        ]);
    }
}
