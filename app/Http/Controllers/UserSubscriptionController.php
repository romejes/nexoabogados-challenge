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
     * @var \App\Services\PlanService
     */
    protected $planService;

    /**
     * Constructor
     *
     * @param UserService $userService
     * @param PlanService $planService
     */
    public function __construct(
        UserService $userService,
        PlanService $planService
    ) {
        $this->userService = $userService;
        $this->planService = $planService;
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
        $plan = $this->planService->getPlanByID($request->input("plan_id"));
        $user = $this->userService->getUserById($userId);

        $subscriptionRegistered = $this->userService->subscribe($user, $plan);

        return response()->json(
            new SubscriptionResource($subscriptionRegistered),
            Response::HTTP_CREATED
        );
    }
}
