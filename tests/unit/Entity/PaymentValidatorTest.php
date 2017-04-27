<?php
namespace Tests\Fei\Service\Payment\Entity;

use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use Fei\Service\Payment\Entity\Context;
use Fei\Service\Payment\Entity\Payment;
use Fei\Service\Payment\Validator\PaymentValidator;
use Ramsey\Uuid\Uuid;

class PaymentValidatorTest extends Unit
{
    public function testValidateId()
    {
        $this->assertTrue((new PaymentValidator('create'))->validateId(null));

        $validator = new PaymentValidator('update');
        $this->assertFalse($validator->validateId('1'));
        $this->assertCount(1, $validator->getErrors());
        $this->assertCount(1, $validator->getErrors()['id']);
        $this->assertEquals(
            'The id has to be an integer',
            reset($validator->getErrors()['id'])
        );

        $validator = new PaymentValidator('create');
        $this->assertFalse($validator->validateId(1));
        $this->assertCount(1, $validator->getErrors());
        $this->assertCount(1, $validator->getErrors()['id']);
        $this->assertEquals(
            'The id cannot be set before creating a Payment',
            reset($validator->getErrors()['id'])
        );
    }

    public function testValidateUuid()
    {
        $uuid = (Uuid::uuid4())->toString();
        $this->assertTrue((new PaymentValidator('create'))->validateUuid($uuid));

        $validator = new PaymentValidator('create');
        $this->assertFalse($validator->validateUuid(''));
        $this->assertCount(1, $validator->getErrors());
        $this->assertCount(1, $validator->getErrors()['uuid']);
        $this->assertEquals(
            'The UUID cannot be an empty string',
            reset($validator->getErrors()['uuid'])
        );

        $this->assertFalse($validator->validateUuid('fake-uuid'));
        $this->assertCount(1, $validator->getErrors());
        $this->assertCount(2, $validator->getErrors()['uuid']);
        $this->assertEquals(
            'The UUID `fake-uuid` is not a valid UUID',
            $validator->getErrors()['uuid'][1]
        );
    }

    public function testValidateCreatedAt()
    {
        $validator = new PaymentValidator('create');
        $this->assertFalse($validator->validateCreatedAt(''));
        $this->assertCount(1, $validator->getErrors());
        $this->assertCount(1, $validator->getErrors()['createdAt']);
        $this->assertEquals(
            'The creation date cannot be empty',
            reset($validator->getErrors()['createdAt'])
        );

        $validator = new PaymentValidator('create');
        $this->assertFalse($validator->validateCreatedAt('fake-date'));
        $this->assertCount(1, $validator->getErrors());
        $this->assertCount(1, $validator->getErrors()['createdAt']);
        $this->assertEquals(
            'The creation date has to be and instance of \DateTime',
            reset($validator->getErrors()['createdAt'])
        );

        $this->assertTrue((new PaymentValidator('create'))->validateCreatedAt(new \DateTime()));
    }

    public function testValidatePayedAt()
    {
        $validator = new PaymentValidator('update');
        $this->assertFalse($validator->validatePayedAt('fake', Payment::STATUS_SETTLED));
        $this->assertCount(1, $validator->getErrors());
        $this->assertCount(1, $validator->getErrors()['payedAt']);
        $this->assertEquals(
            'The payment date has to be and instance of \DateTime',
            reset($validator->getErrors()['payedAt'])
        );

        $validator = new PaymentValidator('update');
        $this->assertFalse($validator->validatePayedAt('fake', Payment::STATUS_CANCELLED));
        $this->assertCount(1, $validator->getErrors());
        $this->assertCount(1, $validator->getErrors()['payedAt']);
        $this->assertEquals(
            'The payment date has to be and instance of \DateTime',
            reset($validator->getErrors()['payedAt'])
        );

        $this->assertTrue(
            (new PaymentValidator('update'))
                ->validatePayedAt(new \DateTime(), Payment::STATUS_CANCELLED)
        );
    }

    public function testValidateExpirationDate()
    {
        $validator = new PaymentValidator('create');
        $this->assertFalse($validator->validateExpirationDate('fake'));
        $this->assertCount(1, $validator->getErrors());
        $this->assertCount(1, $validator->getErrors()['expirationDate']);
        $this->assertEquals(
            'The expiration date has to be and instance of \DateTime',
            reset($validator->getErrors()['expirationDate'])
        );

        $this->assertTrue((new PaymentValidator('create'))->validateExpirationDate(new \DateTime()));
    }

    public function testValidateStatus()
    {
        $validator = new PaymentValidator('create');
        $this->assertFalse($validator->validateStatus(''));
        $this->assertCount(1, $validator->getErrors());
        $this->assertCount(1, $validator->getErrors()['status']);
        $this->assertEquals(
            'The payment status cannot be empty',
            reset($validator->getErrors()['status'])
        );

        $this->assertFalse($validator->validateStatus(67));
        $this->assertCount(1, $validator->getErrors());
        $this->assertCount(2, $validator->getErrors()['status']);
        $this->assertEquals(
            'The payment status has to be one of the following values : ' .
            implode(', ', Payment::getStatuses()),
            $validator->getErrors()['status'][1]
        );

        $this->assertTrue((new PaymentValidator('create'))->validateStatus(Payment::STATUS_CANCELLED));
    }

    public function testValidateCancellationReason()
    {
        $payment = new Payment([
            'status' => Payment::STATUS_CANCELLED
        ]);

        $validator = new PaymentValidator('create');
        $this->assertFalse($validator->validateCancellationReason('', $payment));
        $this->assertEquals(
            'The cancellation reason cannot be an empty string when status is cancelled or rejected',
            reset($validator->getErrors()['cancellationReason'])
        );

        $this->assertTrue(
            (new PaymentValidator('create'))
                ->validateCancellationReason('', $payment->setStatus(Payment::STATUS_PENDING))
        );
    }

    public function testValidateRequiredPrice()
    {
        $validator = new PaymentValidator('create');

        $this->assertFalse($validator->validateRequiredPrice(''));
        $this->assertCount(1, $validator->getErrors());
        $this->assertCount(1, $validator->getErrors()['requiredPrice']);
        $this->assertEquals(
            'The required price cannot be empty',
            reset($validator->getErrors()['requiredPrice'])
        );

        $this->assertFalse($validator->validateRequiredPrice('fake'));
        $this->assertCount(1, $validator->getErrors());
        $this->assertCount(2, $validator->getErrors()['requiredPrice']);
        $this->assertEquals(
            'The required price must be numeric',
            $validator->getErrors()['requiredPrice'][1]
        );

        $this->assertFalse($validator->validateRequiredPrice(-10));
        $this->assertCount(1, $validator->getErrors());
        $this->assertCount(3, $validator->getErrors()['requiredPrice']);
        $this->assertEquals(
            'The required price must be higher or equal to 0',
            $validator->getErrors()['requiredPrice'][2]
        );

        $this->assertTrue((new PaymentValidator('create'))->validateRequiredPrice(10));
    }

    public function testValidateCapturedPrice()
    {
        $payment = new Payment([
            'requiredPrice' => 10
        ]);

        $validator = new PaymentValidator('create');

        $this->assertFalse($validator->validateCapturedPrice('fake', $payment));
        $this->assertCount(1, $validator->getErrors());
        $this->assertCount(1, $validator->getErrors()['capturedPrice']);
        $this->assertEquals(
            'The captured price must be numeric',
            reset($validator->getErrors()['capturedPrice'])
        );

        $this->assertFalse($validator->validateCapturedPrice(-10, $payment));
        $this->assertCount(1, $validator->getErrors());
        $this->assertCount(2, $validator->getErrors()['capturedPrice']);
        $this->assertEquals(
            'The captured price must be higher or equal to 0',
            $validator->getErrors()['capturedPrice'][1]
        );

        $this->assertFalse($validator->validateCapturedPrice(11, $payment));
        $this->assertCount(1, $validator->getErrors());
        $this->assertCount(3, $validator->getErrors()['capturedPrice']);
        $this->assertEquals(
            'The captured price must be lower or equal to the required price',
            $validator->getErrors()['capturedPrice'][2]
        );

        $this->assertTrue((new PaymentValidator('create'))->validateCapturedPrice(5, $payment));
    }

    public function testValidateAuthorizedPayment()
    {
        $validator = new PaymentValidator('create');
        $this->assertFalse($validator->validateAuthorizedPayment(0));
        $this->assertCount(1, $validator->getErrors());
        $this->assertCount(1, $validator->getErrors()['authorizedPayment']);
        $this->assertEquals(
            'The authorized payment bridges cannot be empty',
            reset($validator->getErrors()['authorizedPayment'])
        );

        $this->assertFalse($validator->validateAuthorizedPayment('1'));
        $this->assertCount(1, $validator->getErrors());
        $this->assertCount(2, $validator->getErrors()['authorizedPayment']);
        $this->assertEquals(
            'The authorized payment must be an integer',
            $validator->getErrors()['authorizedPayment'][1]
        );

        $this->assertTrue((new PaymentValidator('create'))->validateAuthorizedPayment(1));
    }

    public function testValidateSelectedPayment()
    {
        $validator = new PaymentValidator('create');
        $this->assertFalse($validator->validateSelectedPayment(null, Payment::STATUS_SETTLED));
        $this->assertCount(1, $validator->getErrors());
        $this->assertCount(1, $validator->getErrors()['selectedPayment']);
        $this->assertEquals(
            'The selected payment bridge has to be an integer',
            reset($validator->getErrors()['selectedPayment'])
        );

        $this->assertTrue($validator->validateSelectedPayment(null, Payment::STATUS_PENDING));

        $this->assertTrue((new PaymentValidator('create'))->validateSelectedPayment(1, Payment::STATUS_PENDING));
    }
}
