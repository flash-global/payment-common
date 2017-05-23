# Payment common

This is the Payment Common elements package which contains:
                                       
* Payment Entity and transformer
* Payment Entity validator
* Related classes

# Installation and Requirement
  
Payment Client needs PHP 5.5 or higher.

Add this requirement to your `composer.json: "fei/payment-common": : "^1.0"`

Or execute `composer.phar require fei/payment-common` in your terminal.

## Entity and classes

## Payment entity

In addition to traditional `id` and `createdAt` fields, Payment entity has eleven important properties:

| Properties    			| Type              |
|---------------------|-------------------|
| id            			| `integer`         |
| uuid          			| `string`          |
| createdAt     			| `datetime`        |
| payedAt     				| `datetime`        |
| expirationDate 			| `datetime`        |
| status 							| `integer`         |
| cancellationReason	| `string`         	|
| requiredPrice       | `float`         	|
| capturedPrice       | `float`         	|
| authorizedPayment 	| `integer`         |
| selectedPayment 		| `integer`         |
| contexts						| `ArrayCollection` |
| callbackUrl					| `ArrayCollection` |

* `uuid` is a string representing a unique identifier of the payment entity
* `createdAt' represent the creation date
* `payedAt' represent the date when the payment has been made
* `expirationDate' represent the date when the payment expires
* `status` indicate in which status the payment currently is
* `cancellationReason` is a string representing the reason of the cancellation of the payment
* `requiredPrice` is a float representing the price required
* `capturedPrice` is a float representing the price captured
* `authorizedPayment` is an int that represent the list of the payment authorised (used like binary flags)
* `selectedPayment` is an integer representing the payment method that has been chosen
* `contexts` is an ArrayCollection of all the contexts for the entity
* `callbackUrl` is an array of callbacks url that will be used is some events in the application (when the payment is saved for example). Here are the possible value and purpose of the callback url:
	* `succeeded` : the url that will be called when an payment authorization successes
	* `failed` : the url that will be called when an payment authorization failed
	* `cancelled` : the url that will be called when an payment is cancelled 

## Context entity

In addition to traditional `id` field, Context entity has three important properties:

| Properties  | Type        |
|-------------|-------------|
| id 					| `integer`   |
| key     		| `string`    |
| value 			| `string`    |
| payment 		| `Payment` 	|

* `key` is a string representing the key of the context
* `value` is a string representing the value attach to this context
* `payment` is a Payment entity representing the Payment related to this context

## Other tools

### Paylment validator

You have the possibility to validate a `Payment` entity with `PaymentValidator` class:

```php
<?php

use Fei\Service\Payment\Validator\PaymentValidator;
use Fei\Service\Payment\Entity\Payment;

$paymentValidator = new PaymentValidator('create');
$payment = new Payment();

//validate returns true if your Payment instance is valid, or false in the other case
$isPaymentValid = $paymentValidator->validate($payment);

//getErrors() allows you to get an array of errors if there are some, or an empty array in the other case
$errors = $paymentValidator->getErrors();
```

By default, only `uuid`, `createdAt`, `expirationDate`, `status`, `requiredPrice`, `authorizedPayment` and `callbackUrl` properties must **not** be empty,
but you're also able to validate only a few properties of your entity, using `validate` methods:

```php
<?php

use Fei\Service\Payment\Validator\PaymentValidator;
use Fei\Service\Payment\Entity\Payment;

$paymentValidator = new PaymentValidator('create');

$payment = new Payment();
$payment->setUuid('uuid');

$paymentValidator->validateUuid($payment->getUuid());

// will return an empty array : all of our definitions are correct
$errors = $paymentValidator->getErrors();
echo empty($errors); // true

// callbackUel can not be empty, let's try to set it as an empty string
$payment->setCallbackUrl([]);
$paymentValidator->validateCallbackUrl($payment->getCallbackUrl());

// this time you'll get a non-empty array
$errors = $paymentValidator->getErrors();

echo empty($errors); // false
print_r($errors);

/**
* print_r will return:
*
*    Array
*    (
*        ['callbackUrl'] => Array
*            (
*                'The callback URL cannot be empty'
*            )
*    )
**/
```

### Context validator

You have the possibility to validate a `Context` entity with `ContextValidator` class:

```php
<?php

use Fei\Service\Payment\Validator\ContextValidator;
use Fei\Service\Payment\Entity\Payment;
use Fei\Service\Payment\Entity\Context;

$contextValidator = new ContextValidator();
$payment = new Payment();
$context = new Context([
    'key' => 'my_key',
    'value' => 'my_value',
    'payment' => $payment
]);

//validate returns true if your Context instance is valid, or false in the other case
$isContextValid = $contextValidator->validate($context);

//getErrors() allows you to get an array of errors if there are some, or an empty array in the other case
$errors = $contextValidator->getErrors();
```

By default, all `Context` properties must **not** be empty,
but you're also able to validate only a few properties of your entity, using `validate` methods:

```php
<?php

use Fei\Service\Payment\Validator\ContextValidator;
use Fei\Service\Payment\Entity\Context;

$contextValidator = new ContextValidator();
$context = new Context();
$context->setKey('key');
$context->setValue('value');

$contextValidator->validateKey($context->getKey());
$contextValidator->validateValue($context->getValue());

// will return an empty array : all of our definitions are correct
$errors = $contextValidator->getErrors();
echo empty($errors); // true
```