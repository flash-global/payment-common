<?php
/**
 * Created by PhpStorm.
 * User: siufong
 * Date: 20/02/18
 * Time: 15:07
 */

namespace Tests\Fei\Service\Payment\Entity;

use Codeception\Test\Unit;
use Fei\Service\Payment\Entity\PlatformConfiguration;

class PlatformConfigurationTest extends Unit
{
    /**
     * @param $property
     * @param $value
     * @dataProvider getterSettersProvider
     */
    public function testGetterSetters($property, $value)
    {
        $platformConfiguration = new PlatformConfiguration();
        $this->assertEquals(
            $value,
            $platformConfiguration->{'set' . ucfirst($property)}($value)->{'get' . ucfirst($property)}()
        );
    }

    public function getterSettersProvider()
    {
        return [
            'id' => ['id', 1],
            'platformId' => ['platformId', 1],
            'key' => ['key', 'passphrase'],
            'value' => ['value', 'thisIsAPassphrase'],
            'updatedAt' => ['updatedAt', '2018-02-20 12:00:00'],
            'updatedBy' => ['updatedBy', 'fakeUser'],
        ];
    }
}
