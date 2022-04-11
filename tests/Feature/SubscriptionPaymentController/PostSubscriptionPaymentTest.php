<?php

namespace Tests\Feature\SubscriptionPaymentController;

use Tests\TestCase;
use App\Models\Subscription;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostSubscriptionPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscription_payment_succesful()
    {
        $subscription = Subscription::factory()->create();

        $url = sprintf('/api/v1/subscriptions/%d/payment', $subscription->id);
        $response = $this->json(Request::METHOD_POST, $url);

        $response->assertCreated();
    }
}
