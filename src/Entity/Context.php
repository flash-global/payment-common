<?php

namespace Fei\Service\Payment\Entity;

use Fei\Entity\AbstractEntity;

/**
 * Class Context
 *
 * @Entity
 * @Table(name="contexts")
 *
 * @package Fei\Service\Payment\Entity
 */
class Context extends AbstractEntity
{
    /**
     * @var int
     *
     * @Id
     * @GeneratedValue(strategy="AUTO")
     * @Column(type="integer")
     */
    protected $id;

    /**
     * @var Payment
     *
     * @ManyToOne(targetEntity="Payment", inversedBy="contexts")
     * @JoinColumn(name="payment_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $payment;

    /**
     * @var string
     *
     * @Column(type="string", name="`key`")
     */
    protected $key;

    /**
     * @var string
     *
     * @Column(type="text", name="`value`")
     */
    protected $value;

    /**
     * Get Id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set Id
     *
     * @param mixed $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get Payment
     *
     * @return Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * Set Payment
     *
     * @param Payment $payment
     *
     * @return $this
     */
    public function setPayment(Payment $payment)
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * Get key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set key
     *
     * @param string $key
     *
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get value
     *
     * @param string $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}
