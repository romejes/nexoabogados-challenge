<?php

namespace App\Services;

use App\Repositories\PlanRepository;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserService
{
    /**
     * @var \App\Repositories\UserRepository
     */
    protected $userRepository;

    /**
     * @var \App\Services\SubscriptionService
     */
    protected $subscriptionService;

    /**
     * Constructor
     *
     * @param \App\Repositories\UserRepository $userRepository
     * @param \App\Services\PlanService $planService
     */
    public function __construct(
        UserRepository $userRepository,
        SubscriptionService $subscriptionService
    ) {
        $this->userRepository = $userRepository;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Obtiene la suscripcion activa de un usuario. Si no posee devolvera una excepcion
     *
     * @param int $userId
     * @return \App\Models\Subscription
     * @throws ModelNotFoundException
     */
    public function getCurrentSubscriptionOfUser($userId)
    {
        $user = $this->getUserById($userId);

        $currentSubscription = $this->userRepository->getCurrentSubscription($user);
        if (!$currentSubscription) {
            throw new ModelNotFoundException("El usuario no posee una suscripcion");
        }

        return $currentSubscription;
    }

    /**
     * Obtiene un usuario mediante su ID
     *
     * @param int $userId
     * @return \App\Models\User
     * @throws ModelNotFoundException
     */
    public function getUserById($userId)
    {
        $user = $this->userRepository->getByID($userId);
        if (!$user) {
            throw new ModelNotFoundException("El usuario no existe");
        }

        return $user;
    }

    /**
     * Cancela la suscripcion por parte del usuario
     *
     * @param int $userId
     * @return void
     */
    public function cancelSubscription($userId)
    {
        $subscription = $this->getCurrentSubscriptionOfUser($userId);

        $this->subscriptionService->cancelSubscription($subscription->id);
    }


    // /**
    //  * Modifica la suscripcion de un usuario.
    //  * Al cambiar de plan, se desuscribe del anterior y crea una nueva suscripcion con el nuevo plan
    //  *
    //  * @param int $userId
    //  * @param int $planId
    //  * @return \App\Models\Subscription
    //  */
    // public function changeSubscriptionPlan($userId, $planId)
    // {
    //     $user = $this->getUser($userId);

    //     $plan = $this->planService->getPlan($planId);

    //     $activeSubscription = $this->getUserCurrentSubscription($userId);

    //     $this->subscriptionService->cancelSubscription($activeSubscription->id);

    //     $newSubscription = $this->subscriptionService->createSubscription($user->id, $plan->id);

    //     return $newSubscription;
    // }

    // /**
    //  * Cancela la subscripcion actual por parte del usuario
    //  *
    //  * @param int $userId
    //  * @return void
    //  */
    // public function unsubscribe($userId)
    // {
    //     $user = $this->getUser($userId);

    //     $activeSubscription = $this->getUserCurrentSubscription($user->id);

    //     $this->subscriptionService->cancelSubscription($activeSubscription->id);
    // }
}
