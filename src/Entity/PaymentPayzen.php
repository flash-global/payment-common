<?php

namespace Fei\Service\Payment\Entity;

use Fei\Entity\AbstractEntity;
use Fei\Service\Payment\Entity\Payment;

/**
 * Class PaymentPayzen
 *
 * @Entity
 *
 * @package Fei\Service\Payment\Entity
 */
class PaymentPayzen extends AbstractEntity
{

    //Payzen Status

    const STATUS_PAYZEN_PAYMENT = [
	"AUTHORISED" => Payment::STATUS_AUTHORIZED,
	"CANCELLED"  => Payment::STATUS_CANCELLED,
	"REFUSED"    => Payment::STATUS_REFUSED,
	"CAPTURE_FAILED" => Payment::STATUS_ERRORED,
	"ABANDONED"	 => Payment::STATUS_ERRORED
    ];

    /**
     * @param string $value
     * @return integer
     */
    public function getPaymentPayzenStatus(string $value)
    {
        return STATUS_PAYZEN_PAYMENT[$value];
    }

}
