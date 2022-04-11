<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Repositories\SubscriptionRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SubscriptionService
{
    /**
     * @var \App\Repositories\SubscriptionRepository
     */
    protected $subscriptionRepository;

    /**
     * @var \App\Services\PaymentService
     */
    protected $paymentService;

    /**
     * Constructor
     *
     * @param SubscriptionRepository $subscriptionRepository
     * @param PaymentService $paymentService
     */
    public function __construct(
        SubscriptionRepository $subscriptionRepository
    ) {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    /**
     * Obtiene suscripciones por estado
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSubscriptions($status = null)
    {
        $subscriptions = [];

        if ($status == null) {
            $subscriptions =  $this->subscriptionRepository->getAll();
        }

        if ($status == config("constants.subscriptions.active")) {
            $subscriptions = $this->getActiveSubscriptions();
        }

        if ($status == config("constants.subscriptions.inactive")) {
            $subscriptions = $this->getInactiveSubscriptions();
        }

        return $subscriptions;
    }

    /**
     * Obtiene solo las suscripciones activas
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getActiveSubscriptions()
    {
        return $this->subscriptionRepository->getActive();
    }

    /**
     * Obtiene solo las suscripciones inactivas
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getInactiveSubscriptions()
    {
        return $this->subscriptionRepository->getInactive();
    }

    /**
     * Obtiene una suscripcion por su ID
     *
     * @param integer $subscriptionId
     * @return \Illuminate\Database\Eloquent\Model
     * @throws ModelNotFoundException
     */
    public function getSubscriptionById($subscriptionId)
    {
        $subscription = $this->subscriptionRepository->getByID($subscriptionId);

        if (!$subscription) {
            throw new ModelNotFoundException("La suscripción no existe");
        }

        return $subscription;
    }

    /**
     * Cancela una suscripcion volviendola inactiva
     *
     * @param int $subscriptionId
     * @return void
     */
    public function cancelSubscription($subscriptionId)
    {
        $subscription = $this->getSubscriptionById($subscriptionId);
        if (!$subscription->is_active) {
            return;
        }

        $subscription = $this->subscriptionRepository->changeToInactive($subscription->id, false);

        if ($subscription->is_active) {
            throw new Exception("No se pudo cancelar la subscripcion");
        }
    }
}
