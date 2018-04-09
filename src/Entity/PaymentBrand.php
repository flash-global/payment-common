<?php

namespace Fei\Service\Payment\Entity;

use Fei\Entity\AbstractEntity;

/**
 * Class PaymentBrand
 *
 * @Entity
 * @Table(name="payments_brand")
 *
 * @package Fei\Service\Payment\Entity
 */
class PaymentBrand extends AbstractEntity
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
     * @var string
     *
     * @Column(type="string", name="logo_url")
     */
    protected $logo_url;

    /**
     * @var bool
     *
     * @Column(type="string", name="css_content")
     */
    protected $css_content;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return PaymentBrand
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getLogoUrl()
    {
        return $this->logo_url;
    }

    /**
     * @param string $name
     * @return PaymentBrand
     */
    public function setLogoUrl($logo_url)
    {
        $this->logo_url = $logo_url;
        return $this;
    }

    /**
     * @return string
     */
    public function getCssContent()
    {
        return $this->css_content;
    }

    /**
     * @param string $name
     * @return PaymentBrand
     */
    public function setCssContent($css_content)
    {
        $this->css_content = $css_content;
        return $this;
    }



}
