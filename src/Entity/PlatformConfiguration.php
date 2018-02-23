<?php

namespace Fei\Service\Payment\Entity;

/**
 * Class PlatformConfiguration
 *
 * @Entity
 * @Table(name="platforms_configuration")
 *
 * @package Fei\Service\Payment\Entity
 */
class PlatformConfiguration
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
     * @var integer
     *
     * @Column(type="integer", name="`platform_id`")
     */
    protected $platformId;

    /**
     * @var string
     *
     * @Column(type="string", name="`key`")
     * */
    protected $key;

    /**
     * @var string
     *
     * @Column(type="string", name="`value`")
     * */
    protected $value;

    /**
     * @var \DateTime
     *
     * @Column(type="datetime", name="`updated_at`")
     * */
    protected $updatedAt;

    /**
     * @var string
     *
     * @Column(type="string", name="`updated_by`")
     * */
    protected $updatedBy;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return PlatformConfiguration
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getPlatformId()
    {
        return $this->platformId;
    }

    /**
     * @param int $platformId
     * @return PlatformConfiguration
     */
    public function setPlatformId($platformId)
    {
        $this->platformId = $platformId;
        return $this;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     * @return PlatformConfiguration
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return PlatformConfiguration
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param string $updatedAt
     * @return PlatformConfiguration
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return string
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * @param string $updatedBy
     * @return PlatformConfiguration
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;
        return $this;
    }
}
