<?php

namespace Fiizy\Api\Model;

/**
 * Store status request.
 */
class StoreStatusRequest
{
    /** @var string ISO 639-1 two-letter language code */
    public $language;
    /** @var Store store data object */
    public $store;
}
