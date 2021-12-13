<?php

namespace Fiizy\Api\Model;

/**
 * Checkout status response data.
 */
class StatusResponse
{
    /** @var string order reference */
    public $reference;
    /** @var string order number */
    public $number;
    /** @var string checkout status */
    public $status;
    /** @var float order amount */
    public $amount;
    /** @var string order currency ISO 4217 code (e.g. USD) */
    public $currency;
    /** @var array<string,string> additional key-value data submitted together with initial checkout request */
    public $metadata;
}
