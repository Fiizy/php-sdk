<?php

namespace Fiizy\Api\Webhook\Model;

/**
 * Order status changed webhook event model.
 */
class OrderStatusChanged
{
    /** @var string order reference */
    public $reference;
    /** @var string order number */
    public $number;
    /** @var OrderStatus order status */
    public $status;
    /** @var float order amount */
    public $amount;
    /** @var string order currency ISO 4217 code (e.g. USD) */
    public $currency;
    /** @var array<string,string> additional key-value data submitted together with initial checkout request */
    public $metadata;
    /** @var string transaction reference */
    public $transactionReference;
}
