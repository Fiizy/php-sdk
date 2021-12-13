<?php

namespace Fiizy\Api\Model;

/**
 * Error container object.
 */
class Error
{
    /** @var string error message */
    public $message;
    /** @var string error code */
    public $error;
    /** @var array<mixed> error data */
    public $data = [];
}
