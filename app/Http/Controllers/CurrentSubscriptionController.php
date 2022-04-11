<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Services\SubscriptionService;
use App\Http\Resources\SubscriptionResource;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\CreateSubscriptionRequest;
use App\Http\Requests\UpdateSubscriptionRequest;

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
     * Constructor
     *
     * @param SubscriptionService $subscriptionService
     * @param UserService $userService
     */
    public function __construct(
        SubscriptionService $subscriptionService,
        UserService $userService
    ) {
        $this->subscriptionService = $subscriptionService;
        $this->userService = $userService;
    }

    /**
     * [GET] users/{id}/subscriptions/current
     *
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function show($userId)
    {
        $subscription = $this->userService->getCurrentSubscriptionOfUser($userId);
        $subscription = new SubscriptionResource($subscription);

        return response()->json($subscription);
    }


    /**
     * [DELETE] users/{id}/subscriptions/current
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($userId)
    {
        $this->userService->cancelSubscription($userId);
        return response()->json([], Response::HTTP_NO_CONTENT);
    }





    // /**
    //  * [POST] users/{id}/subscriptions
    //  *
    //  * @param int $userId
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return \Illuminate\Http\Response
    //  */
    // public function store(CreateSubscriptionRequest $request, $userId)
    // {
    //     $planId = $request->input("plan_id");
    //     $subscriptionRegistered = $this->subscriptionService->createSubscription($userId, $planId);

    //     return response()->json(
    //         new SubscriptionResource($subscriptionRegistered),
    //         Response::HTTP_CREATED
    //     );
    // }



    // /**
    //  * [PUT] users/{id}/subscriptions/current
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function update(UpdateSubscriptionRequest $request, $userId)
    // {
    //     $planId = $request->input("plan_id");
    //     $newSubscription = $this->userService->changeSubscriptionPlan($userId, $planId);

    //     return response()->json(
    //         new SubscriptionResource($newSubscription)
    //     );
    // }

}
