<?php

namespace App\Repositories;

use App\Models\Subscription;

class SubscriptionRepository extends BaseRepository
{
    /**
     * Constructor
     *
     * @param \Illuminate\Database\Eloquent\Model $subscription
     */
    public function __construct(Subscription $subscription)
    {
        parent::__construct($subscription);
    }

    /**
     * Obtiene suscripciones activas
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActive()
    {
        return $this->model->where("is_active", true)->get();
    }

    /**
     * Obtiene suscripciones inactivas
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getInactive()
    {
        return $this->model->where("is_active", false)->get();
    }

    /**
     * Cambia la propiedad is_active y la vuelve inactiva
     *
     * @param int $subscriptionId
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function changeToInactive($subscriptionId)
    {
        return $this->update(["is_active" => false], $subscriptionId);
    }

    /**
     * Obtiene la suscripcion activa de un usuario
     *
     * @param int $userId
     * @param int $planId
     * @return mixed
     */
    public function findActiveSubscriptionByUserId($userId, $planId)
    {
        return $this->model->where([
            "is_active" =>  true,
            "user_id"   =>  $userId,
            "plan_id"   =>  $planId
        ])->first();
    }

    /**
     * Busca un registro de deuda de una suscripcion. Si no encuentra ninguno
     * devuelve NULL
     *
     * @param int $subscriptionId
     * @return mixed
     */
    public function findForDebt($subscription)
    {
        return null;
        //return $subscription->payments->where(["is_paid" => true])->first();
    }
}
