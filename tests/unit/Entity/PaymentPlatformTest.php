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
            'createdAt' => ['createdAt', '2018-02-20 12:00:00'],
        ];
    }

    /** @dataProvider dataProviderStatus */
    public function testIsStatus($status)
    {
        $paymentPlatform = new PaymentPlatform();
        $paymentPlatform->setStatus($status);

        $this->assertEquals($paymentPlatform->isStatus(), $status);
    }

    public function dataProviderStatus()
    {
        return [
            [PaymentPlatform::STATUS_ENABLED],
            [PaymentPlatform::STATUS_DISABLED],
        ];
    }
}
