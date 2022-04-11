<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository
{
    /**
     * Constructor
     *
     * @param \Illuminate\Database\Eloquent\Model $user
     */
    public function __construct(User $user) {
        parent::__construct($user);
    }

    /**
     * Obtiene la suscripcion activa de un usuario
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function getCurrentSubscription(User $user)
    {
        return $user->subscriptions->where("is_active", true)->first();
    }
}
