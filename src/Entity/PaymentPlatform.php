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
    protected $enabled;

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
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     * @return PaymentPlatform
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }
}
