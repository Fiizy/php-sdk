<?php

namespace Fiizy\Api\Model;

/**
 * Order line item type.
 */
final class LineItemType
{
    // Product physical, digital or services
    public const Product = "product";
    // Fee shipping, handling or other fees
    public const Fee = "fee";
    // Discount discounts
    public const Discount = "discount";
}
