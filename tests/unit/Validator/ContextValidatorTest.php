<?php

namespace Tests\Fei\Service\Payment\Validation;

use Codeception\Test\Unit;
use Fei\Entity\Validator\Exception;
use Fei\Service\Payment\Entity\Context;
use Fei\Service\Payment\Entity\Payment;
use Fei\Service\Payment\Validator\ContextValidator;

class ContextValidationTest extends Unit
{
    public function testValidateKey()
    {
        $validator = new ContextValidator();

        $this->assertFalse($validator->validateKey(''));
        $this->assertEquals('The key cannot be empty', $validator->getErrors()['key'][0]);

        $this->assertFalse($validator->validateKey($validator->validateKey(str_repeat('☃', 256))));
        $this->assertEquals('The key length has to be less or equal to 255', $validator->getErrors()['key'][1]);

        $this->assertTrue($validator->validateKey($validator->validateKey(str_repeat('☃', 255))));
    }

    public function testValidateValue()
    {
        $validator = new ContextValidator();

        $this->assertFalse($validator->validateValue(''));
        $this->assertEquals('The value cannot be empty', $validator->getErrors()['value'][0]);

        $this->assertFalse($validator->validateValue(str_pad('My String', 256, 0)));
        $this->assertEquals('The value length has to be less or equal to 255', $validator->getErrors()['value'][1]);

        $this->assertTrue($validator->validateValue('My Value'));
    }

    public function testValidate()
    {
        $validator = new ContextValidator();

        $this->assertFalse($validator->validate(new Context()));

        $validator = new ContextValidator();
        $context = new Context(['key' => 'a key', 'value' => 'a value']);

        $this->assertTrue($validator->validate($context));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            sprintf('The entity to validate must be an instance of %s', Context::class)
        );

        $validator->validate(new Payment());
    }
}
