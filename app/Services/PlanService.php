<?php

namespace App\Services;

use App\Repositories\PlanRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PlanService
{
    protected $planRepository;

    /**
     * Constructor
     *
     * @param PlanRepository $planRepository
     */
    public function __construct(PlanRepository $planRepository) {
        $this->planRepository = $planRepository;
    }

    /**
     * Obtiene un plan de subscripcion mediante su ID
     *
     * @param int $planId
     * @return \App\Models\Plan
     */
    public function getPlanByID($planId)
    {
        $plan = $this->planRepository->getByID($planId);
        if (!$plan) {
            throw new ModelNotFoundException("El plan no existe");
        }

        return $plan;
    }
}
