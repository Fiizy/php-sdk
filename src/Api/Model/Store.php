<?php

namespace Fiizy\Api\Model;

/**
 * Store data object.
 */
class Store
{
    /** @var string store name */
    public $name;
    /** @var string store admin email */
    public $email;
    /** @var string store ISO 3166 alpha-2 country code */
    public $country;
    /** @var string store domain */
    public $domain;
}
