<?php

namespace Tests\Feature\SubscriptionController;

use Tests\TestCase;
use App\Models\Subscription;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetSubscriptionByIdTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_subscription_by_id()
    {
        $subscription = Subscription::factory()->create();

        $url = sprintf("/api/v1/subscriptions/%d", $subscription->id);
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
        $this->assertIsObject($response);
    }

    public function test_subscription_not_found()
    {
        $url = sprintf("/api/v1/subscriptions/%d", 1);
        $response = $this->json(Request::METHOD_GET, $url);

        $response->assertNotFound();
        $this->assertDatabaseCount("subscription", 0);
    }
}
