<?php

namespace Fiizy\Api\Model;

/**
 * Checkout request data.
 */
class CheckoutRequest
{
    /** @var Order */
    public $order;
    /** @var Customer */
    public $customer;
    /** @var Address */
    public $billingAddress;
    /** @var Address */
    public $shippingAddress;
    /** @var Delivery */
    public $delivery;
    /** @var Endpoints */
    public $endpoints;
    /** @var Client */
    public $client;
}
