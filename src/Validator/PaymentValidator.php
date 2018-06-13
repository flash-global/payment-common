<?php

namespace Fei\Service\Payment\Validator;

use Fei\Entity\EntityInterface;
use Fei\Entity\Validator\AbstractValidator;
use Fei\Entity\Validator\Exception;
use Fei\Service\Payment\Entity\Payment;

/**
 * Class PaymentValidator
 *
 * @package Fei\Service\Payment\Validator
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class PaymentValidator extends AbstractValidator
{
    protected $context;

    const UUID_PATTERN =
        '^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$';

    /**
     * PaymentValidator constructor.
     *
     * @param string $context the context of the validation
     */
    public function __construct($context)
    {
        $this->setContext($context);
    }

    /**
     * {@inheritdoc}
     */
    public function validate(EntityInterface $entity)
    {
        if (!$entity instanceof Payment) {
            throw new Exception(sprintf('The entity to validate must be an instance of %s', Payment::class));
        }

        $this->validateId($entity->getId());
        $this->validateUuid($entity->getUuid());
        $this->validateCreatedAt($entity->getCreatedAt());
        $this->validatePayedAt($entity->getPayedAt(), $entity->getStatus());
        $this->validateExpirationDate($entity->getExpirationDate());
        $this->validateStatus($entity->getStatus());
        $this->validateCancellationReason($entity->getCancellationReason(), $entity);
        $this->validateRequiredPrice($entity->getRequiredPrice());
        $this->validateCapturedPrice($entity->getCapturedPrice(), $entity);
        $this->validateAuthorizedPayment($entity->getAuthorizedPayment());
        $this->validateSelectedPayment($entity->getSelectedPayment(), $entity->getStatus());
        $this->validateContexts($entity->getContexts());
        $this->validateCallbackUrl($entity->getCallbackUrl());
        $this->validateVat($entity->getVat());
	$this->validatePaymentMethod($entity->getPaymentMethod());

        return empty($this->getErrors());
    }

    /**
     * Validate the id of the Payment entity
     *
     * @param mixed $id
     *
     * @return bool
     */
    public function validateId($id)
    {
        if (in_array($this->getContext(), ['update', 'delete']) && !is_numeric($id)) {
            $this->addError('id', 'The id has to be an integer');
            return false;
        }

        if ($this->getContext() === 'create' && $id !== null) {
            $this->addError('id', 'The id cannot be set before creating a Payment');
            return false;
        }

        return true;
    }

    /**
     * Validate the uuid of a payment entity
     *
     * @param mixed $uuid
     *
     * @return bool
     */
    public function validateUuid($uuid)
    {
        if (strlen($uuid) === 0 || $uuid === null) {
            $this->addError('uuid', 'The UUID cannot be an empty string');
            return false;
        }

        if (!preg_match('/' . self::UUID_PATTERN . '/', $uuid)) {
            $this->addError('uuid', 'The UUID `' . $uuid  . '` is not a valid UUID');
            return false;
        }

        return true;
    }

    /**
     * Validate the created at date of a payment entity
     *
     * @param mixed $createdAt
     *
     * @return bool
     */
    public function validateCreatedAt($createdAt)
    {
        if (empty($createdAt)) {
            $this->addError('createdAt', 'The creation date cannot be empty');

            return false;
        }

        if (!$createdAt instanceof \DateTime) {
            $this->addError('createdAt', 'The creation date has to be and instance of \DateTime');

            return false;
        }

        return true;
    }

    /**
     * Validate payedAt
     *
     * @param mixed $payedAt
     *
     * @return bool
     */
    public function validatePayedAt($payedAt, $status)
    {
        if ($status === Payment::STATUS_SETTLED && !$payedAt instanceof \DateTime) {
            $this->addError('payedAt', 'The payment date has to be and instance of \DateTime');
            return false;
        }

        if ($payedAt !== null && !$payedAt instanceof \DateTime) {
            $this->addError('payedAt', 'The payment date has to be and instance of \DateTime');
            return false;
        }

        return true;
    }

    /**
     * Validate expirationDate
     *
     * @param mixed $expirationDate
     *
     * @return bool
     */
    public function validateExpirationDate($expirationDate)
    {
        if (!$expirationDate instanceof \DateTime) {
            $this->addError('expirationDate', 'The expiration date has to be and instance of \DateTime');

            return false;
        }

        return true;
    }

     /**
     * Validate methods
     *
     * @param mixed $method
     *
     * @return bool
     */
    public function validatePaymentMethod($method)
    {
        if (!in_array($method, array_keys(Payment::getMethods()))) {
            $this->addError(
                'method',
                'The payment method is unknown ';
            );

            return false;
        }
        return true;
    }

    /**
     * Validate status
     *
     * @param mixed $status
     *
     * @return bool
     */
    public function validateStatus($status)
    {
        if (empty($status)) {
            $this->addError('status', 'The payment status cannot be empty');

            return false;
        }

        if (!in_array($status, array_keys(Payment::getStatuses()))) {
            $this->addError(
                'status',
                'The payment status has to be one of the following values : ' . implode(', ', Payment::getStatuses())
            );

            return false;
        }

        return true;
    }

    /**
     * Validate cancellationReason
     *
     * @param mixed $cancellationReason
     * @param Payment $payment
     *
     * @return bool
     */
    public function validateCancellationReason($cancellationReason, $payment)
    {
        $status = $payment->getStatus();
        if ($status === Payment::STATUS_CANCELLED || $status === Payment::STATUS_REJECTED) {
            if (strlen($cancellationReason) === 0) {
                $this->addError(
                    'cancellationReason',
                    'The cancellation reason cannot be an empty string when status is cancelled or rejected'
                );

                return false;
            }
        }

        return true;
    }

    /**
     * Validator requiredPrice
     *
     * @param mixed $requiredPrice
     *
     * @return bool
     */
    public function validateRequiredPrice($requiredPrice)
    {
        if (empty($requiredPrice)) {
            $this->addError('requiredPrice', 'The required price cannot be empty');

            return false;
        }

        if (!is_numeric($requiredPrice)) {
            $this->addError('requiredPrice', 'The required price must be numeric');

            return false;
        }

        if ($requiredPrice < 0) {
            $this->addError('requiredPrice', 'The required price must be higher or equal to 0');

            return false;
        }

        return true;
    }

    /**
     * Validator capturedPrice
     *
     * @param mixed $capturedPrice
     * @param Payment $payment
     *
     * @return bool
     */
    public function validateCapturedPrice($capturedPrice, $payment)
    {
        if ($capturedPrice !== null && !is_numeric($capturedPrice)) {
            $this->addError('capturedPrice', 'The captured price must be numeric');

            return false;
        }

        if ($capturedPrice < 0) {
            $this->addError('capturedPrice', 'The captured price must be higher or equal to 0');

            return false;
        }

        $requiredPrice = $payment->getRequiredPrice();
        if ($requiredPrice < $capturedPrice) {
            $this->addError('capturedPrice', 'The captured price must be lower or equal to the required price');

            return false;
        }

        return true;
    }

    /**
     * Validator authorizedPayment
     *
     * @param mixed $authorizedPayments
     *
     * @return bool
     */
    public function validateAuthorizedPayment($authorizedPayments)
    {

        if (empty($authorizedPayments)) {
            $this->addError('authorizedPayment', 'The authorized payment bridges cannot be empty');
            return false;
        }

        if (!is_numeric($authorizedPayments)) {
            $this->addError(
                'authorizedPayment',
                'The authorized payment must be an integer'
            );

            return false;
        }

        return true;
    }

    /**
     * Validator selectedPayment
     *
     * @param mixed $selectedPayment
     *
     * @return bool
     */
    public function validateSelectedPayment($selectedPayment, $status)
    {
        if (is_null($selectedPayment) && $status !== Payment::STATUS_SETTLED) {
            return true;
        }

        if (!is_numeric($selectedPayment)) {
            $this->addError('selectedPayment', 'The selected payment bridge has to be an integer');

            return false;
        }

        return true;
    }

    /**
     * Validate contexts
     *
     * @param mixed $context
     *
     * @return bool
     */
    public function validateContexts($context)
    {
        if (!$context->isEmpty()) {
            $validator = new ContextValidator();

            foreach ($context as $value) {
                $validator->validate($value);
            }

            if (!empty($validator->getErrors())) {
                $this->addError('contexts', $validator->getErrorsAsString());

                return false;
            }
        }

        return true;
    }

    /**
     * Validate callbackUrl
     *
     * @param mixed $callbackUrl
     *
     * @return bool
     */
    public function validateCallbackUrl($callbackUrl)
    {
        if (!is_array($callbackUrl)) {
            $this->addError(
                'callbackUrl',
                'The callback URL must be an array'
            );

            return false;
        }

        if (empty($callbackUrl)) {
            $this->addError('callbackUrl', 'The callback URL cannot be empty');

            return false;
        }

        foreach (array_keys($callbackUrl) as $callbackUrlEvent) {
            if (!in_array($callbackUrlEvent, Payment::getCallbackUrlEvents())) {
                $this->addError(
                    'callbackUrl',
                    'The callback URL ' . $callbackUrlEvent . ' is not an authorized value : '
                    . implode(', ', Payment::getCallbackUrlEvents())
                );

                return false;
            }
        }

        if (empty($callbackUrl[Payment::CALLBACK_URL_CANCELED])) {
            $this->addError(
                'callbackUrl',
                'The callback URL for the event cancelled has to be defined'
            );

            return false;
        }

        if (empty($callbackUrl[Payment::CALLBACK_URL_SAVED])) {
            $this->addError(
                'callbackUrl',
                'The callback URL for the event saved has to be defined'
            );

            return false;
        } else {
            $savedCallback = $callbackUrl[Payment::CALLBACK_URL_SAVED];
            if (filter_var($savedCallback, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED) === false) {
                $this->addError(
                    'callbackUrl',
                    'The callback URL for the event saved has to be a valid URL'
                );

                return false;
            }
        }

        return true;
    }

    /**
     * Validator vat
     *
     * @param mixed $vat
     *
     * @return bool
     */
    public function validateVat($vat)
    {
        if (!is_numeric($vat)) {
            $this->addError('vat', 'The VAT must be between 0 and 1');
            return false;
        }

        if ($vat < 0) {
            $this->addError('vat', 'The VAT must be higher than or equals to 0');
            return false;
        }

        if ($vat > 1) {
            $this->addError('vat', 'The VAT must be lower than or equals to 1');

            return false;
        }

        return true;
    }

    /**
     * Get Context
     *
     * @return mixed
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Set Context
     *
     * @param mixed $context
     *
     * @return $this
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }
}
