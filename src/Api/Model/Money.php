<?php

namespace Fiizy\Api\Model;

use JsonSerializable;

class Money implements JsonSerializable
{
    protected $amount;
    protected $currency;

    /**
     * @doc https://en.wikipedia.org/wiki/ISO_4217#Active_codes
     * @param float $amount the amount.
     * @param string $currency the currency, as a 3 letter ISO 4217 code.
     * @throws \Exception when currency is not supported.
     */
    public function __construct($amount, $currency)
    {
        if ($currency !== 'EUR') {
            throw new \Exception('currency not supported');
        }

        $this->amount = $amount;
        $this->currency = $currency;
    }

    /**
     * @return float
     */
    public function jsonSerialize()
    {
        return (float) number_format($this->amount, 4, '.', '');
    }
}
