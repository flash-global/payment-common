<?php

namespace Tests\Fei\Service\Payment\Entity;

use Codeception\Test\Unit;
use Fei\Service\Payment\Entity\PaymentPlatform;

class PaymentPlatformTest extends Unit
{
    /**
     * @param $property
     * @param $value
     * @dataProvider getterSettersProvider
     */
    public function testGetterSetters($property, $value)
    {
        $paymentPlatform = new PaymentPlatform();
        $this->assertEquals(
            $value,
            $paymentPlatform->{'set' . ucfirst($property)}($value)->{'get' . ucfirst($property)}()
        );
    }

    public function getterSettersProvider()
    {
        return [
            'id' => ['id', 1],
            'name' => ['name', 'PlatformPayment1'],
        ];
    }

    /** @dataProvider dataProviderEnabled */
    public function testIsEnabled($enabled)
    {
        $paymentPlatform = new PaymentPlatform();
        $paymentPlatform->setEnabled($enabled);

        $this->assertEquals($paymentPlatform->isEnabled(), $enabled);
    }

    public function dataProviderEnabled()
    {
        return [
            [true],
            [false],
        ];
    }
}
