<?php

namespace Fiizy\Api\Model;

/**
 * Store status response.
 */
class StoreStatusResponse
{
    /** @var StoreStatusCode store status code */
    public $statusCode;
    /** @var string store status label */
    public $statusLabel;
    /** @var string store backoffice link url */
    public $backofficeLinkUrl;
    /** @var string store backoffice link label */
    public $backofficeLinkLabel;
    /** @var string widget url */
    public $widgetUrl;
    /** @var array<string, array<string, string>> translations map */
    public $translations;
}
