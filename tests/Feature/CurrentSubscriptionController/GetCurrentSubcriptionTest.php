<?php

namespace Tests\Feature\CurrentSubscriptionController;

use Tests\TestCase;
use App\Models\User;
use App\Models\Subscription;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetCurrentSubcriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_current_subscription()
    {
        $user = User::factory()->create();
        $subscription = Subscription::factory()->active()->create([
            "user_id"   =>  $user->id
        ]);

        $url = sprintf("/api/v1/users/%d/subscriptions/current", $user->id);
        $response = $this->json(Request::METHOD_GET, $url);

        $response->assertOk();
        $response->assertJsonStructure([
            "id",
            "start_date",
            "expiration_date",
            "is_active",
            "user",
            "plan"
        ]);

        $data = $response->getData();
        $this->assertIsObject($data);
        $this->assertEquals($subscription->id, $data->id);
        $this->assertEquals($subscription->user_id, $data->user->id);
    }

    public function test_user_not_found()
    {
        $url = sprintf("/api/v1/users/%d/subscriptions/current", 1);
        $response = $this->json(Request::METHOD_GET, $url);

        $response->assertNotFound();
        $this->assertDatabaseCount("subscription", 0);
        $this->assertDatabaseCount("user", 0);
    }

    public function test_user_is_not_subscribed()
    {
        $user = User::factory()->create();
        Subscription::factory()->create([
            "is_active" =>  true
        ]);

        $url = sprintf("/api/v1/users/%d/subscriptions/current", $user->id);
        $response = $this->json(Request::METHOD_GET, $url);

        $response->assertNotFound();
        $this->assertDatabaseCount("subscription", 1);
    }
}
