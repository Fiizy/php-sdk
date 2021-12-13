<?php

namespace Fiizy\Api\Model;

/**
 * Delivery data.
 */
class Delivery
{
    /** @var string */
    public $reference;
    /** @var string */
    public $name;
    /** @var string */
    public $carrier;
    /** @var string */
    public $trackingNumber;
    /** @var string */
    public $trackingUrl;
    /** @var \DateTime */
    public $deliveredTimestamp;
}
