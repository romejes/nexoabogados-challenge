<?php

namespace App\Http\Controllers;

use App\Services\SubscriptionService;
use App\Http\Resources\SubscriptionResource;
use App\Http\Requests\GetSubscriptionsRequest;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionController extends Controller
{
    /**
     * @var \App\Services\SubscriptionService
     */
    protected $subscriptionService;

    /**
     * Constructor
     *
     * @param \App\Services\SubscriptionService $subscriptionService
     */
    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * [GET] subscriptions
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(GetSubscriptionsRequest $request)
    {
        $type = $request->input("status");
        $subscriptions = $this->subscriptionService->getSubscriptions($type);
        $subscriptions = SubscriptionResource::collection($subscriptions);

        return response()->json($subscriptions);
    }

    /**
     * [GET] subscriptions/{id}
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $subscription = $this->subscriptionService->getSubscriptionById($id);
        $subscription = new SubscriptionResource($subscription);

        return response()->json($subscription);
    }

    /**
     * [DELETE] subscriptions/{id}
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->subscriptionService->cancelSubscription($id);
        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
