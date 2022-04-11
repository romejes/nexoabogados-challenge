<?php

namespace App\Http\Controllers;

use App\Services\PlanService;
use App\Services\UserService;
use App\Http\Resources\SubscriptionResource;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\CreateSubscriptionRequest;

class UserSubscriptionController extends Controller
{
    /**
     * @var \App\Services\UserService
     */
    protected $userService;

    /**
     * Constructor
     *
     * @param UserService $userService
     */
    public function __construct(
        UserService $userService
    ) {
        $this->userService = $userService;
    }

    /**
     * [POST] users/{id}/subscriptions
     *
     * @param int $userId
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateSubscriptionRequest $request, $userId)
    {
        $planId = $request->input("plan_id");

        $subscriptionRegistered = $this->userService->subscribe($userId, $planId);
        $subscriptionRegistered = new SubscriptionResource($subscriptionRegistered);

        return response()->json($subscriptionRegistered, Response::HTTP_CREATED);
    }
}
