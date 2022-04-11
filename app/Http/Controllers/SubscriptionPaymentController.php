<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use App\Services\SubscriptionService;
use App\Http\Resources\PaymentResource;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionPaymentController extends Controller
{
    /**
     * @var \App\Services\PaymentService
     */
    protected $paymentService;

    /**
     * @var \App\Services\SubscriptionService
     */
    protected $subscriptionService;

    /**
     * Constructor
     *
     * @param PaymentService $paymentService
     * @param SubscriptionService $subscriptionService
     */
    public function __construct(
        PaymentService $paymentService,
        SubscriptionService $subscriptionService
    ) {
        $this->paymentService = $paymentService;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * [POST] subscriptions/{id}/paynent
     *
     * @param int $subscriptionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($subscriptionId)
    {
        $subscription = $this->subscriptionService->getSubscriptionById($subscriptionId);
        $payment = $this->paymentService->performPayment($subscription);
        $payment = new PaymentResource($payment);

        return response()->json($payment, Response::HTTP_CREATED);
    }
}
