<?php

namespace Tests\Fei\Service\Payment\Entity;

use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use Fei\Entity\Validator\Exception;
use Fei\Service\Payment\Entity\Context;
use Fei\Service\Payment\Entity\Payment;

class PaymentTest extends Unit
{
    public function testIdAccessors()
    {
        $payment = new Payment();
        $result  = $payment->setId(1);
        $this->assertInstanceOf(Payment::class, $result);

        $this->assertAttributeEquals($payment->getId(), 'id', $payment);
        $this->assertEquals(1, $payment->getId());
    }

    public function testUidAccessors()
    {
        $payment = new Payment();
        $uid     = uniqid('', true);
        $result  = $payment->setUid($uid);
        $this->assertInstanceOf(Payment::class, $result);

        $this->assertAttributeEquals($payment->getUid(), 'uid', $payment);
        $this->assertEquals($uid, $payment->getUid());
    }

    public function testCreatedAtAccessors()
    {
        $payment = new Payment();
        $date    = new \DateTime('2017-03-01 00:00:00');
        $result  = $payment->setCreatedAt($date);
        $this->assertInstanceOf(Payment::class, $result);

        $this->assertAttributeEquals($payment->getCreatedAt(), 'createdAt', $payment);
        $this->assertEquals($date, $payment->getCreatedAt());
    }

    public function testPayedAtAccessors()
    {
        $payment = new Payment();
        $date    = new \DateTime('2017-03-30 00:00:00');
        $result  = $payment->setPayedAt($date);
        $this->assertInstanceOf(Payment::class, $result);

        $this->assertAttributeEquals($payment->getPayedAt(), 'payedAt', $payment);
        $this->assertEquals($date, $payment->getPayedAt());
    }

    public function testStatusAccessors()
    {
        $payment = new Payment();
        $status  = Payment::STATUS_PENDING;
        $result  = $payment->setStatus($status);
        $this->assertInstanceOf(Payment::class, $result);

        $this->assertAttributeEquals($payment->getStatus(), 'status', $payment);
        $this->assertEquals($status, $payment->getStatus());
    }

    public function testCancellationReasonAccessors()
    {
        $payment            = new Payment();
        $cancellationReason = 'My cancellation reason';
        $result             = $payment->setCancellationReason($cancellationReason);
        $this->assertInstanceOf(Payment::class, $result);

        $this->assertAttributeEquals($payment->getCancellationReason(), 'cancellationReason', $payment);
        $this->assertEquals($cancellationReason, $payment->getCancellationReason());
    }

    public function testRequiredPriceAccessors()
    {
        $payment = new Payment();
        $price   = 19.99;
        $result  = $payment->setRequiredPrice($price);
        $this->assertInstanceOf(Payment::class, $result);

        $this->assertAttributeEquals($payment->getRequiredPrice(), 'requiredPrice', $payment);
        $this->assertEquals($price, $payment->getRequiredPrice());
    }

    public function testCapturedPriceAccessors()
    {
        $payment = new Payment();
        $price   = 15.99;
        $result  = $payment->setCapturedPrice($price);
        $this->assertInstanceOf(Payment::class, $result);

        $this->assertAttributeEquals($payment->getCapturedPrice(), 'capturedPrice', $payment);
        $this->assertEquals($price, $payment->getCapturedPrice());
    }

    public function testAuthorizedPaymentBridgesAccessors()
    {
        $payment = new Payment();
        $bridges = array(
            Payment::PAYMENT_PAYPAL,
            Payment::PAYMENT_CB
        );
        $result  = $payment->setAuthorizedPaymentBridges($bridges);
        $this->assertInstanceOf(Payment::class, $result);

        $this->assertAttributeEquals($payment->getAuthorizedPaymentBridges(), 'authorizedPaymentBridges', $payment);
        $this->assertEquals($bridges, $payment->getAuthorizedPaymentBridges());
    }

    public function testSelectedPaymentBridgeAccessors()
    {
        $payment        = new Payment();
        $selectedBridge = Payment::PAYMENT_PAYPAL;
        $result         = $payment->setSelectedPaymentBridge($selectedBridge);
        $this->assertInstanceOf(Payment::class, $result);

        $this->assertAttributeEquals($payment->getSelectedPaymentBridge(), 'selectedPaymentBridge', $payment);
        $this->assertEquals($selectedBridge, $payment->getSelectedPaymentBridge());
    }

    public function testContextsAccessors()
    {
        $payment = new Payment();
        $context = new Context();

        $result  = $payment->setContexts($context);
        $this->assertInstanceOf(Payment::class, $result);

        $this->assertAttributeEquals($payment->getContexts(), 'contexts', $payment);
        $this->assertEquals(new ArrayCollection([$context]), $payment->getContexts());


        $payment  = new Payment();
        $context1 = new Context();
        $context2 = new Context();

        $payment->setContexts(new ArrayCollection([$context1, $context2]));

        $this->assertAttributeEquals($payment->getContexts(), 'contexts', $payment);
        $this->assertEquals(new ArrayCollection([$context1, $context2]), $payment->getContexts());
    }

    public function testCallbackUrlAccessors()
    {
        $payment     = new Payment();
        $callbackUrl = array(
            Payment::CALLBACK_URL_SUCCEEDED => "http://test.fr",
            Payment::CALLBACK_URL_FAILED    => "http://test.fr/failed",
            Payment::CALLBACK_URL_CANCELED  => "http://test.fr/canceled",
            Payment::CALLBACK_URL_SAVED     => "http://test.fr/saved",
        );
        $result = $payment->setCallbackUrl($callbackUrl);
        $this->assertInstanceOf(Payment::class, $result);

        $this->assertAttributeEquals($payment->getCallbackUrl(), 'callbackUrl', $payment);
        $this->assertEquals($callbackUrl, $payment->getCallbackUrl());


        $callbackUrlSucceeded = 'http://test.fr/succeeded';
        $payment->setCallbackUrlEvent(Payment::CALLBACK_URL_SUCCEEDED, $callbackUrlSucceeded);
        $this->assertEquals($callbackUrlSucceeded, $payment->getCallbackUrl()[Payment::CALLBACK_URL_SUCCEEDED]);


        $this->expectException(Exception::class);
        $payment->setCallbackUrlEvent('test', 'http://test.fr/test');
    }

    public function testGetStatuses()
    {
        $this->assertEquals(
            array(
                Payment::STATUS_PENDING,
                Payment::STATUS_CANCELLED,
                Payment::STATUS_REJECTED,
                Payment::STATUS_AUTHORIZED,
                Payment::STATUS_REFUSED,
                Payment::STATUS_ERRORED,
                Payment::STATUS_SETTLED
            ),
            Payment::getStatuses()
        );
    }

    public function testGetPaymentBridges()
    {
        $this->assertEquals(
            array(
                Payment::PAYMENT_PAYPAL,
                Payment::PAYMENT_CB
            ),
            Payment::getPaymentBridges()
        );
    }

    public function testGetCallbackUrlEvents()
    {
        $this->assertEquals(
            array(
                Payment::CALLBACK_URL_SUCCEEDED,
                Payment::CALLBACK_URL_FAILED,
                Payment::CALLBACK_URL_SAVED,
                Payment::CALLBACK_URL_CANCELED
            ),
            Payment::getCallbackUrlEvents()
        );
    }
}
