<?php

namespace Fiizy\Api\Model;

/**
 * Widget request.
 */
class WidgetRequest
{
    /** @var string widget url */
    public $url;
    /** @var string api public key */
    public $publicKey;
    /** @var Decimal */
    public $amount;
    /** @var string currency ISO 4217 code (e.g. USD) */
    public $currency;
    /** @var string locale used for format settings */
    public $locale;

    /**
     * Get variables from widget request.
     *
     * @return array
     */
    public function variables()
    {
        return array(
            'public_key' => $this->publicKey,
            'amount' => $this->amount->jsonSerialize(),
            'currency' => $this->currency,
            'locale' => $this->locale,
        );
    }
}
