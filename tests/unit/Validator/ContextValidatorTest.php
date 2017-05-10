<?php
namespace Tests\Fei\Service\Payment\Entity;

use Codeception\Test\Unit;
use Codeception\Util\Stub;
use Doctrine\Common\Collections\ArrayCollection;
use Fei\Service\Payment\Entity\Context;
use Fei\Service\Payment\Entity\Payment;
use Fei\Service\Payment\Validator\ContextValidator;
use Fei\Service\Payment\Validator\PaymentValidator;
use Ramsey\Uuid\Uuid;

class ContextValidatorTest extends Unit
{
    public function testValidateKeyWhenKeyIsEmpty()
    {
        $validator = new ContextValidator();

        $validation = $validator->validateKey('');

        $this->assertFalse($validation);
        $this->assertEquals([
            'key' => ['The key cannot be empty']
        ], $validator->getErrors());
    }

    public function testValidateKeyWhenKeyIsToLong()
    {
        $validator = new ContextValidator();

        $validation = $validator->validateKey(str_pad('MyKey', 300, '0'));

        $this->assertFalse($validation);
        $this->assertEquals([
            'key' => ['The key length has to be less or equal to 255']
        ], $validator->getErrors());
    }

    public function testValidateKey()
    {
        $validator = new ContextValidator();

        $validation = $validator->validateKey('my-key');

        $this->assertTrue($validation);
        $this->assertEmpty($validator->getErrors());
    }

    public function testValidateValueWhenIsEmpty()
    {
        $validator = new ContextValidator();

        $validation = $validator->validateValue('');

        $this->assertFalse($validation);
        $this->assertEquals([
            'value' => ['The value cannot be empty']
        ], $validator->getErrors());
    }

    public function testValidateValueWhenValueIsToLong()
    {
        $validator = new ContextValidator();

        $validation = $validator->validateValue(str_pad('MyValue', 300, '0'));

        $this->assertFalse($validation);
        $this->assertEquals([
            'value' => ['The value length has to be less or equal to 255']
        ], $validator->getErrors());
    }

    public function testValidateValue()
    {
        $validator = new ContextValidator();

        $validation = $validator->validateValue('my-value');

        $this->assertTrue($validation);
        $this->assertEmpty($validator->getErrors());
    }

    public function testValidateWhenNotInstanceOfContext()
    {
        $validator = new ContextValidator();

        $this->expectExceptionMessage('The entity to validate must be an instance of ' . Context::class);
        $validator->validate(new Payment());
    }

    public function testValidate()
    {
        /** @var PaymentValidator $validator */
        $validator = Stub::make(ContextValidator::class, [
            'validateValue' => true,
            'validateKey' => true,
            'getError' => []
        ]);

        $validator->validate(
            $this->getMockBuilder(Context::class)->getMock()
        );
    }
}
