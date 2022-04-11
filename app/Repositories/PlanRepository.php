<?php

namespace App\Repositories;

use App\Models\Plan;

class PlanRepository extends BaseRepository
{
    /**
     * Constructor
     *
     * @param \Illuminate\Database\Eloquent\Model $plan
     */
    public function __construct(Plan $plan) {
        parent::__construct($plan);
    }
}
