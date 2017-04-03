<?php

namespace Fei\Service\Payment\Validator;

use Doctrine\Common\Collections\ArrayCollection;
use Fei\Entity\EntityInterface;
use Fei\Entity\Validator\AbstractValidator;
use Fei\Entity\Validator\Exception;
use Fei\Service\Payment\Entity\Payment;

/**
 * Class PaymentValidator
 *
 * @package Fei\Service\Payment\Validator
 */
class PaymentValidator extends AbstractValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate(EntityInterface $entity)
    {
        if (!$entity instanceof Payment) {
            throw new Exception(sprintf('The entity to validate must be an instance of %s', Payment::class));
        }

        $this->validateUid($entity->getUuid());
        $this->validateCreatedAt($entity->getCreatedAt());
        $this->validatePayedAt($entity->getPayedAt());
        $this->validateStatus($entity->getStatus());
        $this->validateCancellationReason($entity->getCancellationReason(), $entity);
        $this->validateRequiredPrice($entity->getRequiredPrice());
        $this->validateCapturedPrice($entity->getCapturedPrice(), $entity);
        $this->validateAuthorizedPayment($entity->getAuthorizedPayment());
        $this->validateSelectedPayment($entity->getSelectedPayment(), $entity);
        $this->validateContexts($entity->getContexts());
        $this->validateCallbackUrl($entity->getCallbackUrl());

        return empty($this->getErrors());
    }

    /**
     * Validate uid
     *
     * @param $uid
     *
     * @return bool
     */
    public function validateUid($uid)
    {
        if (strlen($uid) === 0) {
            $this->addError('uid', 'The UID cannot be an empty string');

            return false;
        }

        if (strlen($uid) < 23 || strlen($uid) > 23) {
            $this->addError('uid', 'The UID `' . $uid  . '` is not a valid UID');

            return false;
        }

        return true;
    }

    /**
     * Validate createdAt
     *
     * @param $createdAt
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
     * @param $payedAt
     *
     * @return bool
     */
    public function validatePayedAt($payedAt)
    {
        if (empty($payedAt)) {
            $this->addError('payedAt', 'The payment date cannot be empty');

            return false;
        }

        if (!$payedAt instanceof \DateTime) {
            $this->addError('payedAt', 'The payment date has to be and instance of \DateTime');

            return false;
        }

        return true;
    }

    /**
     * Validate status
     *
     * @param $status
     *
     * @return bool
     */
    public function validateStatus($status)
    {
        if (empty($status)) {
            $this->addError('status', 'The payment status cannot be empty');

            return false;
        }

        if (!in_array($status, Payment::getStatuses())) {
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
     * @param $cancellationReason
     * @param Payment $payment
     *
     * @return bool
     */
    public function validateCancellationReason($cancellationReason, $payment)
    {
        $status = $payment->getStatus();
        if ($status === Payment::STATUS_CANCELLED || $status === Payment::STATUS_REJECTED) {
            if (strlen($cancellationReason) === 0) {
                $this->addError('cancellationReason', 'The cancellation reason cannot be an empty string');

                return false;
            }
        }

        return true;
    }

    /**
     * Validator requiredPrice
     *
     * @param $requiredPrice
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
     * @param $capturedPrice
     * @param Payment $payment
     *
     * @return bool
     */
    public function validateCapturedPrice($capturedPrice, $payment)
    {
        if (!is_numeric($capturedPrice)) {
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
     * @param $authorizedPayments
     *
     * @return bool
     */
    public function validateAuthorizedPayment($authorizedPayments)
    {
        if (!is_array($authorizedPayments)) {
            $this->addError(
                'authorizedPayment',
                'The authorized payment bridges must be an array'
            );

            return false;
        }

        if (empty($authorizedPayments)) {
            $this->addError('authorizedPayment', 'The authorized payment bridges cannot be empty');

            return false;
        }

        foreach ($authorizedPayments as $authorizedPayment) {
            if (!in_array($authorizedPayment, Payment::getPaymentBridges())) {
                $this->addError(
                    'authorizedPayment',
                    'The authorized payment bridge ' . $authorizedPayment . ' is not an authorized value : '
                    . implode(', ', Payment::getPaymentBridges())
                );

                return false;
            }
        }

        return true;
    }

    /**
     * Validator selectedPayment
     *
     * @param $selectedPayment
     * @param Payment $payment
     *
     * @return bool
     */
    public function validateSelectedPayment($selectedPayment, $payment)
    {
        if (empty($selectedPayment)) {
            $this->addError('selectedPayment', 'The selected payment bridge cannot be empty');

            return false;
        }

        $authorizedPayment = $payment->getAuthorizedPayment();
        if (!in_array($selectedPayment, $authorizedPayment)) {
            $this->addError(
                'selectedPayment',
                'The selected payment bridge has to be one of the authorized payment bridges value'
            );

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
        if (!$context instanceof ArrayCollection) {
            $this->addError(
                'contexts',
                'Context has to be and instance of \Doctrine\Common\Collections\ArrayCollection'
            );
            return false;
        }

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

        foreach (Payment::getCallbackUrlEvents() as $callbackUrlEvent) {
            if (!isset($callbackUrl[$callbackUrlEvent])) {
                $this->addError(
                    'callbackUrl',
                    'The callback URL for the event ' . $callbackUrlEvent . ' must be defined'
                );

                return false;
            }

            if (empty($callbackUrl[$callbackUrlEvent])) {
                $this->addError(
                    'callbackUrl',
                    'The callback URL for the event ' . $callbackUrlEvent . ' can\'t be empty'
                );

                return false;
            }
        }

        return true;
    }
}
