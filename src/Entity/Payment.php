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
    // Payment statuses
    const STATUS_PENDING    = 1;
    const STATUS_AUTHORIZED = 2;
    const STATUS_SETTLED    = 3;
    const STATUS_CANCELLED  = -1;
    const STATUS_REJECTED   = -2;
    const STATUS_REFUSED    = -3;
    const STATUS_OUTDATED   = -4;
    const STATUS_ERRORED    = -5;

    // Authorized payment flags
    const PAYMENT_PAYPAL = 1;
    const PAYMENT_STRIPE = 2;
    const PAYMENT_OGONE = 4;
    const PAYMENT_PAYZEN = 8;

    // Payment callback URL key
    const CALLBACK_URL_SUCCEEDED = "succeeded";
    const CALLBACK_URL_FAILED    = "failed";
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
     * @var \DateTime
     *
     * @Column(type="datetime")
     */
    protected $expirationDate;

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
     * @var int
     *
     * @Column(type="integer")
     *
     * See const PAYMENT_XXX for possible values
     */
    protected $authorizedPayment;

    /**
     * @var int
     *
     * @Column(type="integer", nullable=true)
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
     * @Column(type="array")
     */
    protected $callbackUrl;

    /**
     * @var float
     *
     * @Column(type="float")
     */
    protected $vat;

    /**
     * {@inheritdoc}
     */
    public function __construct($data = null)
    {
        $this->setUuid((Uuid::uuid4())->toString());
        $this->setStatus(self::STATUS_PENDING);
        $this->setCreatedAt(new \DateTime());
        $this->setAuthorizedPayment(0);
        $this->setContexts(new ArrayCollection());
        $this->setCallbackUrl([]);
        $this->setVat(0);

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
        if (is_string($createdAt)) {
            $createdAt = new \DateTime($createdAt);
        }

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
        if (is_string($payedAt)) {
            $payedAt = new \DateTime($payedAt);
        }

        $this->payedAt = $payedAt;

        return $this;
    }

    /**
     * Get ExpirationDate
     *
     * @return \DateTime
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * Set ExpirationDate
     *
     * @param \DateTime|string $expirationDate
     *
     * @return $this
     */
    public function setExpirationDate($expirationDate)
    {
        if (is_string($expirationDate)) {
            $expirationDate = new \DateTime($expirationDate);
        }

        $this->expirationDate = $expirationDate;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return (int)$this->status;
    }

    /**
     * @param int $status
     *
     * @return Payment
     */
    public function setStatus($status)
    {
        $this->status = (int)$status;

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
     * @return int
     */
    public function getAuthorizedPayment()
    {
        return $this->authorizedPayment;
    }

    /**
     * @param int $authorizedPayment
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
            $this->contexts = new ArrayCollection();

            foreach ($context as $key => $value) {
                if ($value instanceof Context) {
                    $value->setPayment($this);
                    $this->contexts->add($value);
                } else {
                    $this->contexts->add(new Context([
                        'key' => $key,
                        'value' => $value,
                        'payment' => $this
                    ]));
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
     * @param array|string $callbackUrl
     *
     * @return Payment
     */
    public function setCallbackUrl($callbackUrl)
    {
        $this->callbackUrl = (is_string($callbackUrl)) ? unserialize($callbackUrl) : $callbackUrl;

        return $this;
    }

    /**
     * @param $event
     * @param $callbackUrlk
     *
     * @return Payment
     * @throws Exception
     */
    public function setCallbackUrlEvent($event, $callbackUrl)
    {
        $callbackUrlEvents = [
            self::CALLBACK_URL_SUCCEEDED,
            self::CALLBACK_URL_FAILED,
            self::CALLBACK_URL_CANCELED
        ];

        if (!in_array($event, $callbackUrlEvents)) {
            throw new Exception('Payment callback URL event ' . $event . ' is undefined.');
        }

        $this->callbackUrl[$event] = $callbackUrl;

        return $this;
    }

    /**
     * Get the Payment's Vat.
     *
     * @return float
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * Set the Payment's Vat.
     *
     * @param float $vat
     * @return $this
     */
    public function setVat($vat)
    {
        $this->vat = $vat;
        return $this;
    }

    /**
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_AUTHORIZED => 'Authorized',
            self::STATUS_REFUSED => 'Refused',
            self::STATUS_OUTDATED => 'Outdated',
            self::STATUS_ERRORED => 'Errored',
            self::STATUS_SETTLED => 'Settled'
        ];
    }

    /**
     * @return array
     */
    public static function getAutorizedPayments()
    {
        return [
            self::PAYMENT_PAYPAL => 'Paypal',
            self::PAYMENT_STRIPE => 'Stripe',
            self::PAYMENT_OGONE => 'Ogone',
            self::PAYMENT_PAYZEN => 'Payzen'
        ];
    }

    /**
     * @return array
     */
    public static function getPaymentBridges()
    {
        return [
            self::PAYMENT_PAYPAL,
            self::PAYMENT_STRIPE,
            self::PAYMENT_OGONE,
            self::PAYMENT_PAYZEN
        ];
    }

    /**
     * @return array
     */
    public static function getCallbackUrlEvents()
    {
        return [
            self::CALLBACK_URL_SUCCEEDED,
            self::CALLBACK_URL_FAILED,
            self::CALLBACK_URL_CANCELED
        ];
    }

    /**
     * @inheritdoc
     */
    public function toArray($mapped = false)
    {
        $properties = array_keys(get_class_vars(self::class));
        $array = [];

        foreach ($properties as $property) {
            $getter = 'get' . $this->toCamelCase($property);

            if (method_exists($this, $getter)) {
                $value = $this->$getter();

                if ($value instanceof \DateTime) {
                    $value = $value->format('c');
                }

                if ($property === 'contexts') {
                    $tmpValue = [];

                    foreach ($value as $v) {
                        $tmpValue[$v->getKey()] = $v->getValue();
                    }

                    $value = $tmpValue;
                }


                $array[$property] = $value;
            }
        }

        return $array;
    }
}
