<?php

namespace Fei\Service\Payment\Entity;

use Fei\Entity\AbstractEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Fei\Entity\Validator\Exception;
use Ramsey\Uuid\Uuid;

/**
 * Class Payment
 *
 * @Entity
 * @Table(name="payments")
 *
 * @package Fei\Service\Payment\Entity
 */
class Payment extends AbstractEntity
{
    /* Payment statuses */
    const STATUS_PENDING    = 1;
    const STATUS_AUTHORIZED = 2;
    const STATUS_SETTLED    = 3;
    const STATUS_CANCELLED  = -1;
    const STATUS_REJECTED   = -2;
    const STATUS_REFUSED    = -3;
    const STATUS_ERRORED    = -4;

    /* Payment authorized bridges */
    const PAYMENT_PAYPAL = 1;
    const PAYMENT_CB     = 2;

    /* Payment callback URL key */
    const CALLBACK_URL_SUCCEEDED = "succeeded";
    const CALLBACK_URL_FAILED    = "failed";
    const CALLBACK_URL_SAVED     = "saved";
    const CALLBACK_URL_CANCELED  = "cancelled";

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
     * @Column(type="string", length=36, unique=true)
     */
    protected $uuid;

    /**
     * @var \DateTime
     *
     * @Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @Column(type="datetime", nullable=true)
     */
    protected $payedAt;

    /**
     * @var int
     *
     * @Column(type="integer")
     *
     * See const STATUS_XXX for possible values
     */
    protected $status;

    /**
     * @var string
     *
     * @Column(type="string", nullable=true)
     */
    protected $cancellationReason;

    /**
     * @var float
     *
     * @Column(type="float")
     */
    protected $requiredPrice;

    /**
     * @var float
     *
     * @Column(type="float", nullable=true)
     */
    protected $capturedPrice;

    /**
     * @var array
     *
     * @Column(type="json")
     *
     * See const PAYMENT_XXX for possible values
     */
    protected $authorizedPayment;

    /**
     * @var int
     *
     * @Column(type="int", nullable=true)
     *
     * See const PAYMENT_XXX for possible values
     */
    protected $selectedPayment;

    /**
     * @var ArrayCollection
     *
     * @OneToMany(targetEntity="Context", mappedBy="payment", cascade={"all"})
     */
    protected $contexts;

    /**
     * @var array
     *
     * @Column(type="json")
     */
    protected $callbackUrl;

    /**
     * {@inheritdoc}
     */
    public function __construct($data = null)
    {
        $this->uuid                      = (Uuid::uuid4())->toString();
        $this->status                   = Payment::STATUS_PENDING;
        $this->createdAt                = new \DateTime();
        $this->authorizedPayment = [];
        $this->contexts                 = new ArrayCollection();
        $this->callbackUrl              = [];

        parent::__construct($data);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     *
     * @return Payment
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @param $uuid
     *
     * @return Payment
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param $createdAt
     *
     * @return Payment
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPayedAt()
    {
        return $this->payedAt;
    }

    /**
     * @param $payedAt
     *
     * @return Payment
     */
    public function setPayedAt($payedAt)
    {
        $this->payedAt = $payedAt;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return Payment
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getCancellationReason()
    {
        return $this->cancellationReason;
    }

    /**
     * @param $cancellationReason
     *
     * @return Payment
     */
    public function setCancellationReason($cancellationReason)
    {
        $this->cancellationReason = $cancellationReason;

        return $this;
    }

    /**
     * @return float
     */
    public function getRequiredPrice()
    {
        return $this->requiredPrice;
    }

    /**
     * @param float $requiredPrice
     *
     * @return Payment
     */
    public function setRequiredPrice($requiredPrice)
    {
        $this->requiredPrice = $requiredPrice;

        return $this;
    }

    /**
     * @return float
     */
    public function getCapturedPrice()
    {
        return $this->capturedPrice;
    }

    /**
     * @param float $capturedPrice
     *
     * @return Payment
     */
    public function setCapturedPrice($capturedPrice)
    {
        $this->capturedPrice = $capturedPrice;

        return $this;
    }

    /**
     * @return array
     */
    public function getAuthorizedPayment()
    {
        return $this->authorizedPayment;
    }

    /**
     * @param array $authorizedPayment
     *
     * @return Payment
     */
    public function setAuthorizedPayment($authorizedPayment)
    {
        $this->authorizedPayment = $authorizedPayment;

        return $this;
    }

    /**
     * @return int
     */
    public function getSelectedPayment()
    {
        return $this->selectedPayment;
    }

    /**
     * @param int $selectedPayment
     *
     * @return Payment
     */
    public function setSelectedPayment($selectedPayment)
    {
        $this->selectedPayment = $selectedPayment;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getContexts()
    {
        return $this->contexts;
    }

    /**
     * @param $context
     *
     * @return Payment
     */
    public function setContexts($context)
    {
        if ($context instanceof Context) {
            $context = [$context];
        }

        if ($context instanceof ArrayCollection || is_array($context)) {
            foreach ($context as $value) {
                if ($value instanceof Context) {
                    $value->setPayment($this);
                    $this->contexts->add($value);
                }
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getCallbackUrl()
    {
        return $this->callbackUrl;
    }

    /**
     * @param array $callbackUrl
     *
     * @return Payment
     */
    public function setCallbackUrl($callbackUrl)
    {
        $this->callbackUrl = $callbackUrl;

        return $this;
    }

    /**
     * @param $event
     * @param $callbackUrl
     *
     * @return Payment
     * @throws Exception
     */
    public function setCallbackUrlEvent($event, $callbackUrl)
    {
        $callbackUrlEvents = [
            self::CALLBACK_URL_SUCCEEDED,
            self::CALLBACK_URL_FAILED,
            self::CALLBACK_URL_SAVED,
            self::CALLBACK_URL_CANCELED
        ];

        if (!in_array($event, $callbackUrlEvents)) {
            throw new Exception('Payment callback URL event ' . $event . ' is undefined.');
        }

        $this->callbackUrl[$event] = $callbackUrl;

        return $this;
    }

    /**
     * @return array
     */
    public static function getStatuses()
    {
        $statuses = [
            Payment::STATUS_PENDING,
            Payment::STATUS_CANCELLED,
            Payment::STATUS_REJECTED,
            Payment::STATUS_AUTHORIZED,
            Payment::STATUS_REFUSED,
            Payment::STATUS_ERRORED,
            Payment::STATUS_SETTLED
        ];

        return $statuses;
    }

    /**
     * @return array
     */
    public static function getPaymentBridges()
    {
        $paymentBridges = [
            Payment::PAYMENT_PAYPAL,
            Payment::PAYMENT_CB
        ];

        return $paymentBridges;
    }

    /**
     * @return array
     */
    public static function getCallbackUrlEvents()
    {
        $callbackUrlEvents = [
            Payment::CALLBACK_URL_SUCCEEDED,
            Payment::CALLBACK_URL_FAILED,
            Payment::CALLBACK_URL_SAVED,
            Payment::CALLBACK_URL_CANCELED
        ];

        return $callbackUrlEvents;
    }
}
