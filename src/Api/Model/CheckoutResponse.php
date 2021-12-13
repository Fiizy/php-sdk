<?php

namespace Fiizy\Api\Model;

/**
 * Checkout response data.
 */
class CheckoutResponse
{
    /** @var string checkout reference hash */
    public $hash;
    /** @var string checkout process redirect url */
    public $redirectUrl;
    /** @var string checkout process embed url */
    public $embedUrl;
}
