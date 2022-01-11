<?php

namespace Fiizy\Api\Model;

/**
 * Order data.
 */
class Order
{
    /** @var string */
    public $reference;
    /** @var string */
    public $number;
    /** @var OrderStatus if cannot be mapped to OrderStatus then status string can be submitted as is */
    public $status;
    /** @var string order currency ISO 4217 code (e.g. USD) */
    public $currency;
    /** @var double */
    public $taxAmount;
    /** @var double */
    public $totalAmount;
    /** @var double */
    public $refundedAmount;
    /** @var array */
    public $metadata;
    /** @var []LineItem */
    public $lineItems;
}
