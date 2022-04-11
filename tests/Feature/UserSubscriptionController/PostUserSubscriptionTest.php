<?php

namespace Tests\Feature\UserSubscriptionController;

use Tests\TestCase;
use App\Models\Plan;
use App\Models\User;
use App\Models\Subscription;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostUserSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_subscription()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $requestBody = [
            "plan_id"   =>  $plan->id
        ];
        $url = sprintf('/api/v1/users/%d/subscriptions', $user->id);
        $response = $this->json(Request::METHOD_POST, $url, $requestBody);

        $response->assertCreated();
        $this->assertDatabaseHas("subscription", [
            "user_id"   =>  $user->id,
            "plan_id"   =>  $plan->id
        ]);
    }

    public function test_method_post_subscription_return_user_not_found()
    {
        //  Given
        $plan = Plan::factory()->create();

        //  When
        $requestBody = [
            "plan_id"   =>  $plan->id
        ];
        $url = sprintf('/api/v1/users/%d/subscriptions', 1);
        $response = $this->json(Request::METHOD_POST, $url, $requestBody);

        //  Asserts
        $response->assertNotFound();
        $this->assertDatabaseMissing("subscription", [
            "user_id"   =>  1,
            "plan_id"   =>  $plan->id
        ]);
    }

    public function test_method_post_subscription_return_request_invalid()
    {
        //  Given
        $user = User::factory()->create();

        //  When
        $requestBody = [
            "plan_id"   =>  1
        ];
        $url = sprintf('/api/v1/users/%d/subscriptions', $user->id);
        $response = $this->json(Request::METHOD_POST, $url, $requestBody);

        //  Asserts
        $response->assertUnprocessable();

        $response->assertJsonStructure([
            "errors"    =>  ["plan_id"]
        ]);

        $this->assertDatabaseMissing("subscription", [
            "user_id"   =>  $user->id,
            "plan_id"   =>  1
        ]);
    }

    public function test_method_post_subscription_return_is_already_subscribed()
    {
        //  Given
        $user = User::factory()->create();
        $plan = Plan::factory()->create();
        Subscription::factory()->create([
            "user_id"   =>  $user->id,
            "plan_id"   =>  $plan->id
        ]);

        //  When
        $requestBody = [
            "plan_id"   =>  $plan->id
        ];
        $url = sprintf('/api/v1/users/%d/subscriptions', $user->id);
        $response = $this->json(Request::METHOD_POST, $url, $requestBody);

        //  Asserts
        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);

        $this->assertDatabaseHas("subscription", [
            "user_id"   =>  $user->id,
            "plan_id"   =>  $plan->id
        ]);
    }
}
