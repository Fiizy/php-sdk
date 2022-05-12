<?php

namespace Fiizy\Api\Model;

use JsonSerializable;

class Decimal implements JsonSerializable
{
    protected $value;
    protected $decimals;

    /**
     * @param float $value the decimal value.
     * @param int $decimals the number of decimal points to preserve on serialization.
     */
    public function __construct($value, $decimals = 4)
    {
        $this->value = $value;
        $this->decimals = $decimals;
    }

    /**
     * @return float
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return (float) number_format($this->value, $this->decimals, '.', '');
    }
}
