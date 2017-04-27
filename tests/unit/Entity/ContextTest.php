<?php
namespace Tests\Fei\Service\Payment\Entity;

use Codeception\Test\Unit;
use Fei\Service\Payment\Entity\Context;
use Fei\Service\Payment\Entity\Payment;

class ContextTest extends Unit
{
    public function testIdAccessors()
    {
        $context = new Context();
        $context->setId(2);

        $this->assertEquals(2, $context->getId());
        $this->assertAttributeEquals($context->getId(), 'id', $context);
    }

    public function testKeyAccessors()
    {
        $context = new Context();
        $context->setKey('fake-key');

        $this->assertEquals('fake-key', $context->getKey());
        $this->assertAttributeEquals($context->getKey(), 'key', $context);
    }

    public function testValueAccessors()
    {
        $context = new Context();
        $context->setValue('fake-value');

        $this->assertEquals('fake-value', $context->getValue());
        $this->assertAttributeEquals($context->getValue(), 'value', $context);
    }

    public function testPaymentAccessors()
    {
        /** @var Payment $paymentMock */
        $paymentMock = $this->getMockBuilder(Payment::class)->getMock();

        $context = new Context();
        $context->setPayment($paymentMock);

        $this->assertEquals($paymentMock, $context->getPayment());
        $this->assertAttributeEquals($context->getPayment(), 'payment', $context);
    }
}
