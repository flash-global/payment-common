<?php

namespace Tests\Fei\Service\Payment\Entity;

use Fei\Service\Payment\Entity\PaymentBrand;
use PHPUnit\Framework\TestCase;

/**
 * Class PaymentBrandTest
 *
 * @package Tests\Fei\Service\Payment\Entity
 */
class PaymentBrandTest extends TestCase
{
    public function testIdAccessors()
    {
        $paymentBrand = new PaymentBrand();

        $paymentBrand->setId(1);

        $this->assertEquals(1, $paymentBrand->getId());
        $this->assertAttributeEquals($paymentBrand->getId(), 'id', $paymentBrand);
    }

    public function testLogoUrl()
    {
        $paymentBrand = new PaymentBrand();

        $paymentBrand->setLogoUrl('http://test');

        $this->assertEquals('http://test', $paymentBrand->getLogoUrl());
        $this->assertAttributeEquals($paymentBrand->getLogoUrl(), 'logo_url', $paymentBrand);
    }

    public function testCssContent()
    {
        $paymentBrand = new PaymentBrand();

        $paymentBrand->setCssContent('test');

        $this->assertEquals('test', $paymentBrand->getCssContent());
        $this->assertAttributeEquals($paymentBrand->getCssContent(), 'css_content', $paymentBrand);
    }
}
