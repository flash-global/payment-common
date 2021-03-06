<?php

namespace Tests\Fei\Service\Payment\Transformer;

use Codeception\Test\Unit;
use Fei\Service\Payment\Entity\Context;
use Fei\Service\Payment\Entity\Payment;
use Fei\Service\Payment\Transformer\PaymentTransformer;
use Ramsey\Uuid\Uuid;

class PaymentTransformerTest extends Unit
{
    public function testTransform()
    {
        $date = new \DateTime('2017-05-01 08:00:00');
        $uuid = (Uuid::uuid4())->toString();

        $payment = new Payment();
        $payment->setId(1)
            ->setUuid($uuid)
            ->setOrderId('XX00000')
            ->setCreatedAt($date)
            ->setPayedAt($date)
            ->setExpirationDate($date)
            ->setStatus(Payment::STATUS_CANCELLED)
            ->setCancellationReason('fake-reason')
            ->setRequiredPrice(3.14159)
            ->setCapturedPrice(2)
            ->setAuthorizedPayment(1)
            ->setSelectedPayment(1)
            ->setVat(0.2)
            ->setContexts(
                (new Context())
                    ->setKey('key')
                    ->setValue('value')
            )
            ->setCallbackUrl([
                'failed' => 'http://fake-url'
            ])
            ->setPaymentMethod('VISA');

        $transformer = new PaymentTransformer();

        $this->assertEquals([
            'id' => 1,
            'uuid' => $uuid,
            'orderId' => 'XX00000',
            'createdAt' => $date->format('c'),
            'payedAt' => $date->format('c'),
            'expirationDate' => $date->format('c'),
            'status' => Payment::STATUS_CANCELLED,
            'cancellationReason' => 'fake-reason',
            'requiredPrice' => 3.14159,
            'capturedPrice' => 2,
            'authorizedPayment' => 1,
            'selectedPayment' => 1,
            'paymentMethod' => Payment::PAYMENT_METHOD_VISA,
            'vat' => 0.2,
            'contexts' => [
                'key' => 'value'
            ],
            'callbackUrl' => [
                'failed' => 'http://fake-url'
            ],
            'refundedPrice' => 0.0,
            'refundPayment' => null
        ], $transformer->transform($payment));
    }
}
