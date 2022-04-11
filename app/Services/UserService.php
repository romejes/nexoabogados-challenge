<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Repositories\PlanRepository;
use App\Repositories\UserRepository;
use App\Repositories\SubscriptionRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserService
{
    /**
     * @var \App\Repositories\UserRepository
     */
    protected $userRepository;

    /**
     * @var \App\Repositories\SubscriptionRepository
     */
    protected $subscriptionRepository;

    /**
     * @var \App\Repositories\PlanRepository
     */
    protected $planRepository;

    /**
     * Constructor
     *
     * @param UserRepository $userRepository
     * @param SubscriptionRepository $subscriptionRepository
     * @param PlanRepository $planRepository
     */
    public function __construct(
        UserRepository $userRepository,
        SubscriptionRepository $subscriptionRepository,
        PlanRepository $planRepository
    ) {
        $this->userRepository = $userRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->planRepository = $planRepository;
    }

    /**
     * Obtiene la suscripcion activa de un usuario. Si no posee devolvera una excepcion
     *
     * @param \App\Models\User $user
     * @return \App\Models\Subscription
     * @throws ModelNotFoundException
     */
    public function getCurrentSubscriptionOfUser(User $user)
    {
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
     * @param \App\Models\User $userId
     * @return void
     */
    public function cancelSubscription(User $user)
    {
        $subscription = $this->getCurrentSubscriptionOfUser($user);

        if (!$subscription->is_active) {
            return;
        }

        $subscription = $this->subscriptionRepository->changeToInactive($subscription->id, false);
    }

    /**
     * Modifica la suscripcion de un usuario.
     * Al cambiar de plan, se desuscribe del anterior y crea una nueva suscripcion con el nuevo plan
     *
     * @param int $userId
     * @param int $planId
     * @return \App\Models\Subscription
     */
    public function changeSubscriptionPlan($userId, $planId)
    {
        $plan = $this->planRepository->getByID($planId);
        if (!$plan) {
            throw new ModelNotFoundException("El plan no existe");
        }

        $user = $this->getUserById($userId);

        $activeSubscription = $this->getCurrentSubscriptionOfUser($user);

        if ($activeSubscription->plan == $plan) {
            throw new BadRequestHttpException("No puedes cambiarte a tu mismo plan actual");
        }

        $this->subscriptionRepository->changeToInactive($activeSubscription->id, false);

        $expirationDate = Carbon::now()->addMinutes(
            config("constants.subscriptions.minutes_for_expiration")
        );

        $newSubscription = $this->subscriptionRepository->insert([
            "user_id"           =>  $user->id,
            "expiration_date"   =>  $expirationDate,
            "is_active"         =>  true,
            "plan_id"           =>  $plan->id
        ]);

        return $newSubscription;
    }

    /**
     * Procedimiento para suscribirse a un plan sin que este suscrito previamente
     * a ningun otro.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Plan $plan
     * @return \App\Models\Subscription
     * @throws BadRequestException
     */
    public function subscribe($userId, $planId)
    {
        $user = $this->getUserById($userId);

        $plan = $this->planRepository->getByID($planId);
        if (!$plan) {
            throw new ModelNotFoundException("El plan no existe");
        }

        $activeSubscription = $this->userRepository->getCurrentSubscription($user);

        if ($activeSubscription) {
            throw new BadRequestException("El usuario ya se encuentra suscrito a un plan");
        }

        $expirationDate = Carbon::now()->addMinutes(
            config("constants.subscriptions.minutes_for_expiration")
        );

        $subscriptionRegistered = $this->subscriptionRepository->insert([
            "user_id"           =>  $user->id,
            "expiration_date"   =>  $expirationDate,
            "is_active"         =>  true,
            "plan_id"           =>  $plan->id
        ]);

        return $subscriptionRegistered;
    }
}
