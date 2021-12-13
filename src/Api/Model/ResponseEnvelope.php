<?php

namespace Fiizy\Api\Model;

/**
 * Generic API response envelope.
 */
class ResponseEnvelope
{
    /** @var bool request result */
    public $success;
    /** @var array<Error> request errors */
    public $errors = [];
    /** @var mixed response data */
    public $data;
}
