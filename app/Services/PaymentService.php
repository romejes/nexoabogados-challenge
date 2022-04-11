<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Payment;
use App\Models\Subscription;
use App\Repositories\PaymentRepository;
use App\Repositories\SubscriptionRepository;

class PaymentService
{
    /**
     * @var \App\Repositories\PaymentRepository
     */
    protected $paymentRepository;

    /**
     * @var \App\Repositories\SubscriptionRepository
     */
    protected $subscriptionRepository;

    /**
     * Constructor
     *
     * @param PaymentRepository $paymentRepository
     * @param SubscriptionRepository $subscriptionRepository
     */
    public function __construct(
        PaymentRepository $paymentRepository,
        SubscriptionRepository $subscriptionRepository
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->subscriptionRepository = $subscriptionRepository;
    }

    /**
     * Realiza el proceso de pago de una suscripcion
     *
     * @param Subscription $subscription
     * @return \App\Models\Payment
     */
    public function performPayment(Subscription $subscription)
    {
        $debt = $this->subscriptionRepository->findForDebt($subscription);

        $maxAttempts = config("constants.subscriptions.max_attempts_for_payment");

        for ($i = 0; $i < $maxAttempts; $i++) {
            $paymentIsSuccesful = (bool)random_int(0, 1);

            if ($paymentIsSuccesful) {
                break;
            }
        }

        if ($paymentIsSuccesful && !$debt) {
            $payment = $this->registerSuccesfulPayment($subscription);
        } else if ($paymentIsSuccesful && $debt) {
            $payment = $this->switchToPaid($debt);
        } else if (!$paymentIsSuccesful && !$debt) {
            $payment = $this->registerFailedPayment($subscription);
        } else {
            $payment = $this->increaseAttemptsOnFailedPayment($debt);
        }

        return $payment;
    }

    /**
     * Ingresa un nuevo registro de pago exitoso
     *
     * @param \App\Models\Subscription $subscription
     * @return \App\Models\Payment
     */
    private function registerSuccesfulPayment(Subscription $subscription)
    {
        $expirationDate = Carbon::now()->addMinutes(
            config("constants.subscriptions.minutes_for_expiration")
        );

        $this->subscriptionRepository->update([
            "expiration_date"   =>  $expirationDate
        ], $subscription->id);

        $payment = $this->paymentRepository->insert([
            "subscription_id"   =>  $subscription->id,
            "is_paid"           =>  true,
            "payment_date"      =>  Carbon::now(),
            "attempts"          =>  null
        ]);

        return $payment;
    }

    /**
     * Modifica el estado de pago fallido a exitoso
     *
     * @param int $paymentId
     * @return \App\Models\Payment
     */
    private function switchToPaid($paymentId)
    {
        return $this->paymentRepository->update([
            "is_paid"           =>  true,
            "payment_date"      =>  Carbon::now(),
            "attempts"          =>  null
        ], $paymentId);
    }

    /**
     * Ingresa un nuevo registro de pago fallido
     *
     * @param \App\Models\Subscription $subscription
     * @return \App\Models\Payment
     */
    private function registerFailedPayment(Subscription $subscription)
    {
        return $this->paymentRepository->insert([
            "subscription_id"   =>  $subscription->id,
            "is_paid"           =>  false,
            "payment_date"      =>  null,
            "attempts"          =>  1
        ]);
    }

    /**
     * Actualiza la cantidad de intentos
     *
     * @param Payment $payment
     * @return \App\Models\Payment
     */
    private function increaseAttemptsOnFailedPayment(Payment $payment)
    {
        $maxAttemptsToUnsubscribe = config("constants.subscriptions.max_attempts_for_unsubscription");

        $attempts = $payment->attempts < $maxAttemptsToUnsubscribe ?
            $payment->attempts++ :
            $maxAttemptsToUnsubscribe;

        return $this->paymentRepository->update([
            "attempts"  =>  $attempts
        ], $payment->id);
    }
}
