<?php
namespace Tests\Fei\Service\Payment\Entity;

use Codeception\Test\Unit;
use Fei\Service\Payment\Entity\Context;
use Fei\Service\Payment\Entity\Payment;

class ContextTest extends Unit
{
    public function testId()
    {
        $context = new Context();
        $context->setId(1);

        $this->assertEquals(1, $context->getId());
        $this->assertAttributeEquals($context->getId(), 'id', $context);
    }

    public function testKey()
    {
        $context = new Context();
        $context->setKey(1);

        $this->assertEquals(1, $context->getKey());
        $this->assertAttributeEquals($context->getKey(), 'key', $context);
    }

    public function testValue()
    {
        $context = new Context();
        $context->setValue(1);

        $this->assertEquals(1, $context->getValue());
        $this->assertAttributeEquals($context->getValue(), 'value', $context);
    }

    public function testPayment()
    {
        $expected = new Payment();
        $context  = new Context();
        $context->setPayment($expected);

        $this->assertEquals($expected, $context->getPayment());
        $this->assertAttributeEquals($context->getPayment(), 'payment', $context);
    }
}
