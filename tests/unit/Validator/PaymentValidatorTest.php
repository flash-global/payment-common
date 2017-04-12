<?php

namespace Tests\Fei\Service\Payment\Validation;

use Codeception\Test\Unit;
use Codeception\Util\Stub;
use Doctrine\Common\Collections\ArrayCollection;
use Fei\Entity\Validator\Exception;
use Fei\Service\Payment\Entity\Context;
use Fei\Service\Payment\Entity\Payment;
use Fei\Service\Payment\Validator\PaymentValidator;
use Ramsey\Uuid\Uuid;

class PaymentValidatorTest extends Unit
{
    public function testValidateWhenEntityIsNotAnInstanceOfPayment()
    {
        $validator = new PaymentValidator();

        $this->expectException(Exception::class);
        $this->assertFalse($validator->validate(new Context()));
    }

    public function testValidateWithError()
    {
        $validator = Stub::make(PaymentValidator::class, [
            'getErrors' => ['errors']
        ]);

        $payment = $this->getPaymentEntrity();
        $results = $validator->validate($payment);
        $this->assertFalse($results);
    }

    public function testValidateWithoutError()
    {
        $validator = Stub::make(PaymentValidator::class, [
            'getErrors' => []
        ]);

        $payment = $this->getPaymentEntrity();

        $results = $validator->validate($payment);
        $this->assertTrue($results);
    }

    public function testValidateUuid()
    {
        $validator = new PaymentValidator();

        $this->assertFalse($validator->validateUuid(null));
        $this->assertEquals('The UUID cannot be an empty string', $validator->getErrors()['uuid'][0]);

        $this->assertFalse($validator->validateUuid(''));
        $this->assertEquals('The UUID cannot be an empty string', $validator->getErrors()['uuid'][1]);

        $this->assertFalse($validator->validateUuid('MyUid'));
        $this->assertEquals('The UUID `MyUid` is not a valid UUID', $validator->getErrors()['uuid'][2]);

        $this->assertFalse($validator->validateUuid('MySuuuuuuuuuuuperLongUid'));
        $this->assertEquals(
            'The UUID `MySuuuuuuuuuuuperLongUid` is not a valid UUID',
            $validator->getErrors()['uuid'][3]
        );

        $this->assertFalse($validator->validateUuid(uniqid('test', true)));
        $this->assertFalse($validator->validateUuid(uniqid('', false)));

        $this->assertTrue($validator->validateUuid((Uuid::uuid4())->toString()));
    }

    public function testValidateCreatedAt()
    {
        $validator = new PaymentValidator();

        $this->assertFalse($validator->validateCreatedAt(null));
        $this->assertEquals('The creation date cannot be empty', $validator->getErrors()['createdAt'][0]);

        $this->assertFalse($validator->validateCreatedAt(''));
        $this->assertEquals('The creation date cannot be empty', $validator->getErrors()['createdAt'][1]);

        $this->assertFalse($validator->validateCreatedAt('01/01/2017 00:00:00'));
        $this->assertEquals(
            'The creation date has to be and instance of \DateTime',
            $validator->getErrors()['createdAt'][2]
        );

        $this->assertTrue($validator->validateCreatedAt(new \DateTime('now')));
    }

    public function testValidatePayedAt()
    {
        $validator = new PaymentValidator();

        $this->assertTrue($validator->validatePayedAt(null));

        $this->assertFalse($validator->validatePayedAt(''));
        $this->assertEquals(
            'The payment date has to be and instance of \DateTime',
            $validator->getErrors()['payedAt'][0]
        );

        $this->assertFalse($validator->validatePayedAt('01/01/2017 00:00:00'));
        $this->assertEquals(
            'The payment date has to be and instance of \DateTime',
            $validator->getErrors()['payedAt'][1]
        );

        $this->assertTrue($validator->validatePayedAt(new \DateTime('now')));
    }

    public function testValidateStatus()
    {
        $validator = new PaymentValidator();

        $this->assertFalse($validator->validateStatus(null));
        $this->assertEquals('The payment status cannot be empty', $validator->getErrors()['status'][0]);

        $this->assertFalse($validator->validateStatus(''));
        $this->assertEquals('The payment status cannot be empty', $validator->getErrors()['status'][1]);

        $this->assertFalse($validator->validateStatus('MyStatus'));
        $this->assertEquals(
            'The payment status has to be one of the following values : '
            . implode(', ', Payment::getStatuses()),
            $validator->getErrors()['status'][2]
        );

        $this->assertTrue($validator->validateStatus(Payment::STATUS_CANCELLED));
    }

    public function testValidateCancellationReason()
    {
        $validator = new PaymentValidator();

        $payment = new Payment();
        $payment->setStatus(Payment::STATUS_CANCELLED);

        $this->assertFalse($validator->validateCancellationReason(null, $payment));
        $this->assertEquals(
            'The cancellation reason cannot be an empty string',
            $validator->getErrors()['cancellationReason'][0]
        );

        $this->assertFalse($validator->validateCancellationReason('', $payment));
        $this->assertEquals(
            'The cancellation reason cannot be an empty string',
            $validator->getErrors()['cancellationReason'][1]
        );

        $this->assertTrue($validator->validateCancellationReason('MyReason', $payment));


        $payment->setStatus(Payment::STATUS_AUTHORIZED);

        $this->assertTrue($validator->validateCancellationReason(null, $payment));

        $this->assertTrue($validator->validateCancellationReason('', $payment));

        $this->assertTrue($validator->validateCancellationReason('MyReason', $payment));
    }

    public function testValidateRequiredPrice()
    {
        $validator = new PaymentValidator();

        $this->assertFalse($validator->validateRequiredPrice(null));
        $this->assertEquals('The required price cannot be empty', $validator->getErrors()['requiredPrice'][0]);

        $this->assertFalse($validator->validateRequiredPrice(''));
        $this->assertEquals('The required price cannot be empty', $validator->getErrors()['requiredPrice'][1]);

        $this->assertFalse($validator->validateRequiredPrice('MyPrice'));
        $this->assertEquals('The required price must be numeric', $validator->getErrors()['requiredPrice'][2]);

        $this->assertFalse($validator->validateRequiredPrice(-500.30));
        $this->assertEquals(
            'The required price must be higher or equal to 0',
            $validator->getErrors()['requiredPrice'][3]
        );

        $this->assertTrue($validator->validateRequiredPrice(500.30));
    }

    public function testValidateCapturedPrice()
    {
        $validator = new PaymentValidator();

        $payment = new Payment();
        $payment->setRequiredPrice(500.30);

        $this->assertTrue($validator->validateCapturedPrice(null, $payment));

        $this->assertFalse($validator->validateCapturedPrice('', $payment));
        $this->assertEquals('The captured price must be numeric', $validator->getErrors()['capturedPrice'][0]);

        $this->assertFalse($validator->validateCapturedPrice('MyPrice', $payment));
        $this->assertEquals('The captured price must be numeric', $validator->getErrors()['capturedPrice'][1]);

        $this->assertFalse($validator->validateCapturedPrice(-400.30, $payment));
        $this->assertEquals(
            'The captured price must be higher or equal to 0',
            $validator->getErrors()['capturedPrice'][2]
        );

        $this->assertFalse($validator->validateCapturedPrice(600.30, $payment));
        $this->assertEquals(
            'The captured price must be lower or equal to the required price',
            $validator->getErrors()['capturedPrice'][3]
        );

        $this->assertTrue($validator->validateCapturedPrice(400.30, $payment));
    }

    public function testValidateAuthorizedPayment()
    {
        $validator = new PaymentValidator();

        $this->assertFalse($validator->validateAuthorizedPayment(null));

        $this->assertFalse($validator->validateAuthorizedPayment(''));
        $this->assertEquals(
            'The authorized payment bridges must be an integer',
            $validator->getErrors()['authorizedPayment'][1]
        );

        $this->assertFalse($validator->validateAuthorizedPayment('MyAuthorizedPaymentBridge'));
        $this->assertEquals(
            'The authorized payment bridges cannot be empty',
            $validator->getErrors()['authorizedPayment'][2]
        );

        $this->assertFalse($validator->validateAuthorizedPayment(new ArrayCollection()));
        $this->assertEquals(
            'The authorized payment bridges must be an integer',
            $validator->getErrors()['authorizedPayment'][3]
        );

        $this->assertFalse($validator->validateAuthorizedPayment(array()));
        $this->assertEquals(
            'The authorized payment bridges must be an integer',
            $validator->getErrors()['authorizedPayment'][4]
        );

        $this->assertTrue($validator->validateAuthorizedPayment(Payment::PAYMENT_CB));
    }

    public function testValidateSelectedPayment()
    {
        $validator = new PaymentValidator();

        $payment = new Payment();
        $payment->setAuthorizedPayment(Payment::PAYMENT_CB);

        $this->assertTrue($validator->validateSelectedPayment(null));

        $this->assertFalse($validator->validateSelectedPayment(''));
        $this->assertEquals(
            'The selected payment bridge has to be an integer',
            $validator->getErrors()['selectedPayment'][0]
        );

        $this->assertFalse($validator->validateSelectedPayment('MyAuthorizedPaymentBridge'));
        $this->assertEquals(
            'The selected payment bridge has to be an integer',
            $validator->getErrors()['selectedPayment'][1]
        );

        $this->assertTrue($validator->validateSelectedPayment(Payment::PAYMENT_PAYPAL));

        $this->assertTrue($validator->validateSelectedPayment(Payment::PAYMENT_CB));
    }

    public function testValidateContexts()
    {
        $validator = new PaymentValidator();

        $this->assertFalse($validator->validateContexts([]));
        $this->assertEquals(
            'Context has to be and instance of \Doctrine\Common\Collections\ArrayCollection',
            $validator->getErrors()['contexts'][0]
        );

        $contexts = new ArrayCollection([new Context()]);
        $this->assertFalse($validator->validateContexts($contexts));

        $contexts = new ArrayCollection([(new Context())
            ->setKey('key')
            ->setValue('val')
        ]);
        $this->assertTrue($validator->validateContexts($contexts));

        $this->assertTrue($validator->validateContexts(new ArrayCollection()));

        $contexts = new ArrayCollection();
        $contexts->add('value');

        $this->expectException(\TypeError::class);
        $validator->validateContexts($contexts);
    }

    public function testValidateCallbackUrl()
    {
        $validator = new PaymentValidator();

        $this->assertFalse($validator->validateCallbackUrl(null));
        $this->assertEquals('The callback URL must be an array', $validator->getErrors()['callbackUrl'][0]);

        $this->assertFalse($validator->validateCallbackUrl(''));
        $this->assertEquals('The callback URL must be an array', $validator->getErrors()['callbackUrl'][1]);

        $this->assertFalse($validator->validateCallbackUrl('MyAuthorizedPaymentBridge'));
        $this->assertEquals('The callback URL must be an array', $validator->getErrors()['callbackUrl'][2]);

        $this->assertFalse($validator->validateCallbackUrl(new ArrayCollection()));
        $this->assertEquals('The callback URL must be an array', $validator->getErrors()['callbackUrl'][3]);

        $this->assertFalse($validator->validateCallbackUrl(array()));
        $this->assertEquals('The callback URL cannot be empty', $validator->getErrors()['callbackUrl'][4]);

        $this->assertFalse($validator->validateCallbackUrl(['MyCallbackUrl' => '']));
        $this->assertEquals(
            'The callback URL MyCallbackUrl is not an authorized value : '
            . implode(', ', Payment::getCallbackUrlEvents()),
            $validator->getErrors()['callbackUrl'][5]
        );

        $this->assertFalse(
            $validator->validateCallbackUrl([
                Payment::CALLBACK_URL_SUCCEEDED => 'MyCallbackUrlSucceeded'])
        );
        $this->assertEquals(
            'The callback URL for the event ' . Payment::CALLBACK_URL_FAILED . ' must be defined',
            $validator->getErrors()['callbackUrl'][6]
        );

        $this->assertFalse($validator->validateCallbackUrl([
            Payment::CALLBACK_URL_SUCCEEDED => '',
            Payment::CALLBACK_URL_FAILED    => '',
            Payment::CALLBACK_URL_CANCELED  => '',
            Payment::CALLBACK_URL_SAVED     => '',
        ]));
        $this->assertEquals(
            'The callback URL for the event ' . Payment::CALLBACK_URL_SUCCEEDED . ' can\'t be empty',
            $validator->getErrors()['callbackUrl'][7]
        );

        $this->assertTrue($validator->validateCallbackUrl([
            Payment::CALLBACK_URL_SUCCEEDED => "MyCallbackUrlSucceeded",
            Payment::CALLBACK_URL_FAILED    => "MyCallbackUrlFailed",
            Payment::CALLBACK_URL_CANCELED  => "MyCallbackUrlCanceled",
            Payment::CALLBACK_URL_SAVED     => "MyCallbackUrlSaved"
        ]));
    }

    protected function getPaymentEntrity()
    {
        $payment = new Payment();
        $payment->setId(1);
        $payment->setPayedAt(new \DateTime('2017-01-01 00:00:00'));
        $payment->setStatus(Payment::STATUS_CANCELLED);
        $payment->setCancellationReason('My cancellation reason');
        $payment->setRequiredPrice(150.50);
        $payment->setCapturedPrice(119.99);
        $payment->setAuthorizedPayment([Payment::PAYMENT_CB]);
        $payment->setSelectedPayment(Payment::PAYMENT_CB);
        $payment->setContexts([]);
        $payment->setCallbackUrl(array(
            Payment::CALLBACK_URL_SUCCEEDED => "MyCallbackUrlSucceeded",
            Payment::CALLBACK_URL_FAILED    => "MyCallbackUrlFailed",
            Payment::CALLBACK_URL_CANCELED  => "MyCallbackUrlCanceled",
            Payment::CALLBACK_URL_SAVED     => "MyCallbackUrlSaved"
        ));

        return $payment;
    }
}
