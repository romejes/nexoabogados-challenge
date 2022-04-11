<?php

namespace Tests\Feature\SubscriptionController;

use Tests\TestCase;
use App\Models\Subscription;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetSubscriptionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_subscriptions()
    {
        Subscription::factory()->count(10)->create();

        $url = "/api/v1/subscriptions";
        $response = $this->json(Request::METHOD_GET, $url);

        $response->assertOk();
        $response->assertJsonCount(10);
        $response->assertJsonStructure([
            "*" =>  [
                "id",
                "start_date",
                "expiration_date",
                "is_active",
                "user",
                "plan"
            ]
        ]);

        $this->assertIsArray($response->getData());
    }

    public function test_get_active_subscriptions()
    {
        Subscription::factory()->active()->count(4)->create();
        Subscription::factory()->inactive()->count(8)->create();

        $url = "/api/v1/subscriptions?status=active";
        $response = $this->json(Request::METHOD_GET, $url);

        $response->assertOk();
        $response->assertJsonCount(4);
        $response->assertJsonStructure([
            "*" =>  [
                "id",
                "start_date",
                "expiration_date",
                "is_active",
                "user",
                "plan"
            ]
        ]);
        $this->assertIsArray($response->getData());
    }

    public function test_get_inactive_subscriptions()
    {
        Subscription::factory()->active()->count(4)->create();
        Subscription::factory()->inactive()->count(8)->create();

        $url = "/api/v1/subscriptions?status=inactive";
        $response = $this->json(Request::METHOD_GET, $url);

        $response->assertOk();
        $response->assertJsonCount(8);
        $response->assertJsonStructure([
            "*" =>  [
                "id",
                "start_date",
                "expiration_date",
                "is_active",
                "user",
                "plan"
            ]
        ]);
        $this->assertIsArray($response->getData());
    }

    public function test_invalid_parameter()
    {
        Subscription::factory()->count(4)->create();

        $url = "/api/v1/subscriptions?status=inacti";
        $response = $this->json(Request::METHOD_GET, $url);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(["status"]);
    }
}
