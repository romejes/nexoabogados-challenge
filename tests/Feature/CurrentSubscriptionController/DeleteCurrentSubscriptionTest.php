<?php

namespace Tests\Feature\CurrentSubscriptionController;

use Tests\TestCase;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteCurrentSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_cancel_current_subscription()
    {
        $user = User::factory()->create();
        $subscription = Subscription::factory()->active()->create([
            "user_id"   =>  $user->id
        ]);

        $url = sprintf("/api/v1/users/%d/subscriptions/current", $user->id);
        $response = $this->json(Request::METHOD_DELETE, $url);

        $response->assertNoContent();
        $this->assertEmpty($response->getData());

        $this->assertDatabaseHas("subscription", [
            "is_active" =>  false,
            "id"        =>  $subscription->id,
            "user_id"   =>  $user->id
        ]);

        $this->assertDatabaseMissing("subscription", [
            "is_active" =>  true,
            "id"        =>  $subscription->id,
            "user_id"   =>  $user->id
        ]);
    }

    public function test_user_not_found()
    {
        Subscription::factory()->active()->create();

        $url = sprintf("/api/v1/users/%d/subscriptions/current", 1);
        $response = $this->json(Request::METHOD_DELETE, $url);

        $response->assertNotFound();
        $this->assertDatabaseCount("subscription", 1);
    }

    public function test_user_has_not_active_subscription()
    {
        $user = User::factory()->create();
        Subscription::factory()->inactive()->create([
            "user_id"   =>  $user->id
        ]);

        $url = sprintf("/api/v1/users/%d/subscriptions/current", $user->id);
        $response = $this->json(Request::METHOD_DELETE, $url);

        $response->assertNotFound();
        $this->assertDatabaseCount("subscription", 1);
    }
}
