<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\PlanRepository;
use App\Repositories\UserRepository;
use Exception;
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
     * @var \App\Services\SubscriptionService
     */
    protected $subscriptionService;

    /**
     * @var \App\Services\PlanService
     */
    protected $planService;

    /**
     * Constructor
     *
     * @param UserRepository $userRepository
     * @param SubscriptionService $subscriptionService
     * @param PlanService $planService
     */
    public function __construct(
        UserRepository $userRepository,
        SubscriptionService $subscriptionService,
        PlanService $planService
    ) {
        $this->userRepository = $userRepository;
        $this->subscriptionService = $subscriptionService;
        $this->planService = $planService;
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

        $this->subscriptionService->cancelSubscription($subscription->id);
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
        $plan = $this->planService->getPlanByID($planId);

        $user = $this->getUserById($userId);

        $activeSubscription = $this->getCurrentSubscriptionOfUser($user);

        if ($activeSubscription->plan == $plan) {
            throw new BadRequestHttpException("No puedes cambiarte a tu mismo plan actual");
        }

        $this->subscriptionService->cancelSubscription($activeSubscription->id);

        $newSubscription = $this->subscriptionService->createSubscription($user, $plan);

        return $newSubscription;
    }
}
