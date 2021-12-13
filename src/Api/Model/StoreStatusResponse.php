<?php

namespace Fiizy\Api\Model;

/**
 * Store status response.
 */
class StoreStatusResponse
{
    /** @var StoreStatusCode store status code */
    public $statusCode = '';
    /** @var string store status label */
    public $statusLabel = '';
    /** @var string store backoffice link url */
    public $backofficeLinkUrl = '';
    /** @var string store backoffice link label */
    public $backofficeLinkLabel = '';
}
