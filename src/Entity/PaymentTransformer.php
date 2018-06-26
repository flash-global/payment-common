<?php
namespace Fei\Service\Payment\Entity;

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
            'orderid' => $payment->getOrderId(),
            'createdAt' => $payment->getCreatedAt()->format('c'),
            'payedAt' => ($payedAt instanceof \DateTime) ? $payedAt->format('c') : $payedAt,
            'expirationDate' => $payment->getExpirationDate()->format('c'),
            'status' => $payment->getStatus(),
            'cancellationReason' => $payment->getCancellationReason(),
            'requiredPrice' => $payment->getRequiredPrice(),
            'capturedPrice' => $payment->getCapturedPrice(),
            'authorizedPayment' => $payment->getAuthorizedPayment(),
            'selectedPayment' => $payment->getSelectedPayment(),
            'methodPayment' => $payment->getPaymentMethod(),
            'vat' => $payment->getVat(),
            'contexts' => $contextItems,
            'callbackUrl' => $payment->getCallbackUrl()
        ];
    }
}
