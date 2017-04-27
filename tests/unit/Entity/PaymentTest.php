<?php
namespace Tests\Fei\Service\Payment\Entity;

use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use Fei\Service\Payment\Entity\Context;
use Fei\Service\Payment\Entity\Payment;
use Ramsey\Uuid\Uuid;

class PaymentTest extends Unit
{
    public function testIdAccessors()
    {
        $payment = new Payment();
        $payment->setId(2);

        $this->assertEquals(2, $payment->getId());
        $this->assertAttributeEquals($payment->getId(), 'id', $payment);
    }

    public function testUuidAccessors()
    {
        $payment = new Payment();
        $payment->setUuid('fake-uuid');

        $this->assertEquals('fake-uuid', $payment->getUuid());
        $this->assertAttributeEquals($payment->getUuid(), 'uuid', $payment);
    }

    public function testCreatedAtAccessors()
    {
        $datetimeMock = $this->getMockBuilder(\DateTime::class)->getMock();

        $payment = new Payment();
        $payment->setCreatedAt($datetimeMock);

        $this->assertEquals($datetimeMock, $payment->getCreatedAt());
        $this->assertAttributeEquals($payment->getCreatedAt(), 'createdAt', $payment);
    }

    public function testCreatedAtWhenStringIsGiven()
    {
        $expected = new \DateTime('2017-05-01 08:00:00');

        $payment = new Payment();
        $payment->setCreatedAt('2017-05-01 08:00:00');

        $this->assertEquals($expected, $payment->getCreatedAt());
    }

    public function testPayedAtAccessors()
    {
        $datetimeMock = $this->getMockBuilder(\DateTime::class)->getMock();

        $payment = new Payment();
        $payment->setPayedAt($datetimeMock);

        $this->assertEquals($datetimeMock, $payment->getPayedAt());
        $this->assertAttributeEquals($payment->getPayedAt(), 'payedAt', $payment);
    }

    public function testPayedAtWhenStringIsGiven()
    {
        $expected = new \DateTime('2017-05-01 08:00:00');

        $payment = new Payment();
        $payment->setPayedAt('2017-05-01 08:00:00');

        $this->assertEquals($expected, $payment->getPayedAt());
    }

    public function testExpirationDateAccessors()
    {
        $datetimeMock = $this->getMockBuilder(\DateTime::class)->getMock();

        $payment = new Payment();
        $payment->setExpirationDate($datetimeMock);

        $this->assertEquals($datetimeMock, $payment->getExpirationDate());
        $this->assertAttributeEquals($payment->getExpirationDate(), 'expirationDate', $payment);
    }

    public function testExpirationDateWhenStringIsGiven()
    {
        $expected = new \DateTime('2017-05-01 08:00:00');

        $payment = new Payment();
        $payment->setExpirationDate('2017-05-01 08:00:00');

        $this->assertEquals($expected, $payment->getExpirationDate());
    }

    public function testStatusAccessors()
    {
        $payment = new Payment();
        $payment->setStatus(Payment::STATUS_CANCELLED);

        $this->assertEquals(Payment::STATUS_CANCELLED, $payment->getStatus());
        $this->assertAttributeEquals($payment->getStatus(), 'status', $payment);
    }

    public function testCancellationReasonAccessors()
    {
        $payment = new Payment();
        $payment->setCancellationReason('fake-reason');

        $this->assertEquals('fake-reason', $payment->getCancellationReason());
        $this->assertAttributeEquals($payment->getCancellationReason(), 'cancellationReason', $payment);
    }

    public function testRequiredPriceAccessors()
    {
        $payment = new Payment();
        $payment->setRequiredPrice(3.14159);

        $this->assertEquals(3.14159, $payment->getRequiredPrice());
        $this->assertAttributeEquals($payment->getRequiredPrice(), 'requiredPrice', $payment);
    }

    public function testCapturedPriceAccessors()
    {
        $payment = new Payment();
        $payment->setCapturedPrice(3.14159);

        $this->assertEquals(3.14159, $payment->getCapturedPrice());
        $this->assertAttributeEquals($payment->getCapturedPrice(), 'capturedPrice', $payment);
    }

    public function testAuthorizedPaymentAccessors()
    {
        $payment = new Payment();
        $payment->setAuthorizedPayment(2);

        $this->assertEquals(2, $payment->getAuthorizedPayment());
        $this->assertAttributeEquals($payment->getAuthorizedPayment(), 'authorizedPayment', $payment);
    }

    public function testSelectedPaymentAccessors()
    {
        $payment = new Payment();
        $payment->setSelectedPayment(2);

        $this->assertEquals(2, $payment->getSelectedPayment());
        $this->assertAttributeEquals($payment->getSelectedPayment(), 'selectedPayment', $payment);
    }

    public function testCallbackUrlAccessors()
    {
        $payment = new Payment();
        $payment->setCallbackUrl(['saved' => 'fake-url']);

        $this->assertEquals(['saved' => 'fake-url'], $payment->getCallbackUrl());
        $this->assertAttributeEquals($payment->getCallbackUrl(), 'callbackUrl', $payment);
    }

    public function testSetCallbackUrlEvent()
    {
        $payment = new Payment();
        $payment->setCallbackUrlEvent(Payment::CALLBACK_URL_SAVED, 'http://fake-url');

        $expected = $payment->getCallbackUrl();

        $this->assertEquals('http://fake-url', $expected[Payment::CALLBACK_URL_SAVED]);
    }

    public function testSetCallbackUrlEventWhenEventDoesNotExists()
    {
        $payment = new Payment();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Payment callback URL event not-existing-event is undefined.');

        $payment->setCallbackUrlEvent('not-existing-event', 'http://fake-url');
    }

    public function testContextsAccessors()
    {

        $payment = new Payment();
        $arrayCollection = new ArrayCollection([new Context(['key' => 'value'])]);
        $payment->setContexts($arrayCollection);

        $this->assertEquals($arrayCollection, $payment->getContexts());
        $this->assertAttributeEquals($payment->getContexts(), 'contexts', $payment);
    }

    public function testSetContextsWhenInstanceOfContext()
    {
        $context = (new Context())
            ->setKey('key')
            ->setValue('val');

        $payment = new Payment();
        $payment->setContexts($context);

        $ar = new ArrayCollection();
        $ar->add($context);

        $this->assertEquals($ar, $payment->getContexts());
    }

    public function testSetContextsWhenArrayIsGiven()
    {
        $payment = new Payment();
        $payment->setContexts(['key' => 'value']);

        $ar = new ArrayCollection();
        $ar->add(
            (new Context())
                ->setKey('key')
                ->setValue('value')
                ->setPayment($payment)
        );

        $this->assertEquals($ar, $payment->getContexts());
    }

    public function testGetStatuses()
    {
        $this->assertEquals([
            Payment::STATUS_PENDING => 'Pending',
            Payment::STATUS_CANCELLED => 'Cancelled',
            Payment::STATUS_REJECTED => 'Rejected',
            Payment::STATUS_AUTHORIZED => 'Authorized',
            Payment::STATUS_REFUSED => 'Refused',
            Payment::STATUS_OUTDATED => 'Outdated',
            Payment::STATUS_ERRORED => 'Errored',
            Payment::STATUS_SETTLED => 'Settled'
        ], Payment::getStatuses());
    }

    public function testGetAuthorizedPayments()
    {
        $this->assertEquals([
            Payment::PAYMENT_PAYPAL => 'Paypal',
            Payment::PAYMENT_STRIPE => 'Stripe',
            Payment::PAYMENT_OGONE => 'Ogone',
            Payment::PAYMENT_PAYZEN => 'Payzen'
        ], Payment::getAutorizedPayments());
    }

    public function testGetPaymentBridges()
    {
        $this->assertEquals([
            Payment::PAYMENT_PAYPAL,
            Payment::PAYMENT_STRIPE,
            Payment::PAYMENT_OGONE,
            Payment::PAYMENT_PAYZEN
        ], Payment::getPaymentBridges());
    }

    public function testGetCallbackUrlEvents()
    {
        $this->assertEquals([
            Payment::CALLBACK_URL_SUCCEEDED,
            Payment::CALLBACK_URL_FAILED,
            Payment::CALLBACK_URL_SAVED,
            Payment::CALLBACK_URL_CANCELED
        ], Payment::getCallbackUrlEvents());
    }

    public function testToArray()
    {
        $date = new \DateTime('2017-05-01 08:00:00');
        $uuid = (Uuid::uuid4())->toString();

        $payment = new Payment();
        $payment->setId(1)
            ->setUuid($uuid)
            ->setCreatedAt($date)
            ->setPayedAt($date)
            ->setExpirationDate($date)
            ->setStatus(Payment::STATUS_CANCELLED)
            ->setCancellationReason('fake-reason')
            ->setRequiredPrice(3.14159)
            ->setCapturedPrice(2)
            ->setAuthorizedPayment(1)
            ->setSelectedPayment(1)
            ->setContexts(
                (new Context())
                    ->setKey('key')
                    ->setValue('value')
            )
            ->setCallbackUrl([
                'saved' => 'http://fake-url'
            ]);

        $this->assertEquals([
            'id' => 1,
            'uuid' => $uuid,
            'createdAt' => $date->format('c'),
            'payedAt' => $date->format('c'),
            'expirationDate' => $date->format('c'),
            'status' => Payment::STATUS_CANCELLED,
            'cancellationReason' => 'fake-reason',
            'requiredPrice' => 3.14159,
            'capturedPrice' => 2,
            'authorizedPayment' => 1,
            'selectedPayment' => 1,
            'contexts' => [
                'key' => 'value'
            ],
            'callbackUrl' => [
                'saved' => 'http://fake-url'
            ],
        ], $payment->toArray());
    }
}
