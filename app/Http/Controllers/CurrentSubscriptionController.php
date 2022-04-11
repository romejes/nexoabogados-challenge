<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Services\SubscriptionService;
use App\Http\Resources\SubscriptionResource;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\UpdateSubscriptionRequest;
use App\Services\PlanService;

class CurrentSubscriptionController extends Controller
{
    /**
     * @var \App\Services\SubscriptionService
     */
    protected $subscriptionService;

    /**
     * @var \App\Services\UserService
     */
    protected $userService;

    /**
     * @var \App\Services\PlanService
     */
    protected $planService;

    /**
     * Constructor
     *
     * @param SubscriptionService $subscriptionService
     * @param UserService $userService
     */
    public function __construct(
        SubscriptionService $subscriptionService,
        UserService $userService,
        PlanService $planService
    ) {
        $this->subscriptionService = $subscriptionService;
        $this->userService = $userService;
        $this->planService = $planService;
    }

    /**
     * [GET] users/{id}/subscriptions/current
     *
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function show($userId)
    {
        $user = $this->userService->getUserById($userId);
        $subscription = $this->userService->getCurrentSubscriptionOfUser($user);
        $subscription = new SubscriptionResource($subscription);

        return response()->json($subscription);
    }

    /**
     * [PUT] users/{id}/subscriptions/current
     *
     * @param UpdateSubscriptionRequest $request
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateSubscriptionRequest $request, $userId)
    {
        $planId = $request->input("plan_id");
        $newSubscription = $this->userService->changeSubscriptionPlan($userId, $planId);
        $newSubscription = new SubscriptionResource($newSubscription);

        return response()->json($newSubscription);
    }

    /**
     * [DELETE] users/{id}/subscriptions/current
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($userId)
    {
        $user = $this->userService->getUserById($userId);
        $this->userService->cancelSubscription($user);
        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
