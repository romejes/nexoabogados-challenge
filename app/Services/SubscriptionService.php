<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use App\Repositories\PlanRepository;
use App\Repositories\UserRepository;
use App\Repositories\SubscriptionRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SubscriptionService
{
    /**
     * @var \App\Repositories\SubscriptionRepository
     */
    protected $subscriptionRepository;

    /**
     * Constructor
     *
     * @param SubscriptionRepository $subscriptionRepository
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






    // /**
    //  * @var \App\Repositories\UserRepository
    //  */
    // protected $userRepository;

    // /**
    //  * @var \App\Repositories\PlanRepository
    //  */
    // protected $planRepository;

    // /**
    //  * @var \App\Services\UserService
    //  */
    // protected $userService;

    // /**
    //  * Constructor
    //  *
    //  * @param \App\Repositories\SubscriptionRepository $subscriptionRepository
    //  * @param \App\Repositories\UserRepository $userRepository
    //  * @param \App\Repositories\PlanRepository $planRepository
    //  */
    // public function __construct(
    //     SubscriptionRepository $subscriptionRepository,
    //     UserRepository $userRepository,
    //     PlanRepository $planRepository,
    //     UserService $userService
    // ) {
    //     $this->subscriptionRepository = $subscriptionRepository;
    //     $this->userRepository = $userRepository;
    //     $this->planRepository = $planRepository;
    //     $this->userService = $userService;
    // }







    // /**
    //  * Cancela una suscripcion
    //  *
    //  * @param int $subscriptionId
    //  * @return boolean
    //  */
    // public function cancelSubscription($subscriptionId)
    // {
    //     $subscription = $this->getSubscriptionById($subscriptionId);
    //     $modifiedSubscription = $this->subscriptionRepository->toggleIsActive($subscription->id, false);

    //     if ($modifiedSubscription->is_active) {
    //         throw new Exception("No se pudo cancelar la subscripcion");
    //     }

    //     return true;
    // }

    // /**
    //  * Crea una suscripcion para un usuario
    //  *
    //  * @param int $userId
    //  * @param int $planId
    //  * @return \Illuminate\Database\Eloquent\Model
    //  */
    // public function createSubscription($userId, $planId)
    // {
    //     $user = $this->userRepository->getByID($userId);
    //     if (!$user) {
    //         throw new ModelNotFoundException("El usuario no existe");
    //     }

    //     $plan = $this->planRepository->getByID($planId);
    //     if (!$plan) {
    //         throw new ModelNotFoundException("El plan no existe");
    //     }

    //     //  TODO: Create a custom exception for this
    //     $currentSubscription = $this->userRepository->getActiveSubscription($user->id);
    //     if ($currentSubscription && $currentSubscription->plan_id === $plan->id) {
    //         throw new Exception("No puedes suscribirte a un plan que estas usando en este momento");
    //     }

    //     $expirationDate = $this->setExpirationDate();
    //     $subscriptionRegistered = $this->subscriptionRepository->insert([
    //         "user_id"           =>  $userId,
    //         "expiration_date"   =>  $expirationDate,
    //         "is_active"         =>  true,
    //         "plan_id"           =>  $planId
    //     ]);

    //     return $subscriptionRegistered;
    // }

    // /**
    //  * Establece la fecha y hora de vencimiento de una suscripcion
    //  *
    //  * @return \DateTime
    //  */
    // private function setExpirationDate()
    // {
    //     return Carbon::now()->addMinutes(
    //         config("constants.subscriptions.minutes_for_expiration")
    //     );
    // }
}
