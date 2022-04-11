<?php

namespace App\Repositories;

use App\Models\Plan;

class PlanRepository extends BaseRepository
{
    public function __construct(Plan $plan) {
        parent::__construct($plan);
    }
}
