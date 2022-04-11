<?php

namespace App\Repositories;

use App\Models\Payment;

class PaymentRepository extends BaseRepository
{
    public function __construct(Payment $payment) {
        parent::__construct($payment);
    }

    /**
     * Busca un registro de deuda de pago
     *
     * @param int $subscriptionId
     * @return mixed
     */
    public function findDebtPaymentForSubscription($subscriptionId)
    {
        return $this->model->where([
            "subscription_id"   =>  $subscriptionId,
            "is_paid"           =>  false
        ])->first();
    }
}
