<?php

namespace Fei\Service\Payment\Transformer;

use Fei\Service\Payment\Entity\Context;
use Fei\Service\Payment\Entity\Payment;
use League\Fractal\TransformerAbstract;

/**
 * Class PaymentTransformer
 *
 * @package Fei\Service\Payment\Entity
 */
class PaymentTransformer extends TransformerAbstract
{
    public function transform(Payment $payment)
    {
        $contextItems = [];

        /** @var Context $contextItem */
        foreach ($payment->getContexts() as $contextItem) {
            $contextItems[$contextItem->getKey()] = $contextItem->getValue();
        }

        $payedAt = $payment->getPayedAt();

        return [
            'id' => (int)$payment->getId(),
            'uuid' => $payment->getUuid(),
            'orderId' => $payment->getOrderId(),
            'createdAt' => $payment->getCreatedAt()->format('c'),
            'payedAt' => ($payedAt instanceof \DateTime) ? $payedAt->format('c') : $payedAt,
            'expirationDate' => $payment->getExpirationDate()->format('c'),
            'status' => $payment->getStatus(),
            'cancellationReason' => $payment->getCancellationReason(),
            'requiredPrice' => $payment->getRequiredPrice(),
            'capturedPrice' => $payment->getCapturedPrice(),
            'authorizedPayment' => $payment->getAuthorizedPayment(),
            'selectedPayment' => $payment->getSelectedPayment(),
            'paymentMethod' => $payment->getPaymentMethod(),
            'vat' => $payment->getVat(),
            'contexts' => $contextItems,
            'callbackUrl' => $payment->getCallbackUrl(),
            'refundedPrice' => $payment->getRefundedPrice(),
            'refundPayment' => $payment->getRefundPayment() ? $this->transform($payment->getRefundPayment()) : null
        ];
    }
}
