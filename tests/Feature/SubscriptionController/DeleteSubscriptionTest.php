<?php

namespace Tests\Feature\SubscriptionController;

use App\Models\Subscription;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_cancel_subscription()
    {
        $subscription = Subscription::factory()->create();

        $url = sprintf("/api/v1/subscriptions/%d", $subscription->id);
        $response = $this->json(Request::METHOD_DELETE, $url);

        $response->assertNoContent();
        $this->assertEmpty($response->getData());

        $this->assertDatabaseHas("subscription", [
            "is_active" =>  false,
            "id"        =>  $subscription->id
        ]);

        $this->assertDatabaseMissing("subscription", [
            "is_active" =>  true,
            "id"        =>  $subscription->id
        ]);
    }

    public function test_subscription_not_found()
    {
        $url = sprintf("/api/v1/subscriptions/%d", 1);
        $response = $this->json(Request::METHOD_DELETE, $url);

        $response->assertNotFound();
        $this->assertDatabaseCount("subscription", 0);
    }
}
