<?php

namespace Fiizy\Api\Model;

/**
 * Order list item data.
 */
class LineItem
{
    /** @var LineItemType */
    public $type;
    /** @var LineItemSubType */
    public $subType;
    /** @var string */
    public $reference;
    /** @var LineItemStatus */
    public $status;
    /** @var string */
    public $name;
    /** @var string */
    public $url;
    /** @var string */
    public $imageUrl;
    /** @var string */
    public $description;
    /** @var integer */
    public $quantity;
    /** @var Decimal */
    public $price;
    /** @var Decimal */
    public $taxRate;
    /** @var Decimal */
    public $totalDiscountAmount;
    /** @var Decimal */
    public $totalAmount;
    /** @var array */
    public $metadata;
}
