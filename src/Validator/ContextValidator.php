<?php

namespace Fei\Service\Payment\Validator;

use Fei\Entity\EntityInterface;
use Fei\Entity\Validator\AbstractValidator;
use Fei\Entity\Validator\Exception;
use Fei\Service\Payment\Entity\Context;

/**
 * Class ContextValidator
 *
 * @package Fei\Service\Payment\Validator
 */
class ContextValidator extends AbstractValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate(EntityInterface $entity)
    {
        if (!$entity instanceof Context) {
            throw new Exception(sprintf('The entity to validate must be an instance of %s', Context::class));
        }

        $this->validateKey($entity->getKey());
        $this->validateValue($entity->getValue());

        return empty($this->getErrors());
    }

    /**
     * Validate key
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function validateKey($key)
    {
        if (empty($key) && $key !== 0) {
            $this->addError('key', 'The key cannot be empty');

            return false;
        }

        if (mb_strlen($key, 'UTF-8') > 255) {
            $this->addError('key', 'The key length has to be less or equal to 255');

            return false;
        }

        return true;
    }

    /**
     * Validate value
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function validateValue($value)
    {
        if (empty($value) && $value !== 0) {
            $this->addError('value', 'The value cannot be empty');

            return false;
        }

        if (mb_strlen($value, 'UTF-8') > 255) {
            $this->addError('value', 'The value length has to be less or equal to 255');

            return false;
        }

        return true;
    }
}
