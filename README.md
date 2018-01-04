# Service Payment - Common

[![GitHub release](https://img.shields.io/github/release/flash-global/payment-common.svg?style=for-the-badge)](README.md)

## Table of contents
- [Entities](#entities)
- [Validators](#validators)
- [Contribution](#contribution)

## Entities

### Payment entity

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
* `createdAt` represent the creation date
* `payedAt` represent the date when the payment has been made
* `expirationDate` represent the date when the payment expires
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

### Context entity

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


## Validators


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

## Contribution
As FEI Service, designed and made by OpCoding. The contribution workflow will involve both technical teams. Feel free to contribute, to improve features and apply patches, but keep in mind to carefully deal with pull request. Merging must be the product of complete discussions between Flash and OpCoding teams :) 