<?php

namespace Fei\Service\Payment\Entity;

/**
 * Class PaymentPlatform
 *
 * @Entity
 * @Table(name="payments_platforms")
 *
 * @package Fei\Service\Payment\Entity
 */
class PaymentPlatform
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * @var int
     *
     * @Id
     * @GeneratedValue(strategy="AUTO")
     * @Column(type="integer")
     */
    protected $id;

    /** @var string  */
    protected $name;

    /** @var bool */
    protected $status;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return PaymentPlatform
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return PaymentPlatform
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return bool
     */
    public function isStatus()
    {
        return $this->status;
    }

    /**
     * @param bool $status
     * @return PaymentPlatform
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }
}
