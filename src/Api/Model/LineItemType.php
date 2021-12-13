<?php

namespace Fiizy\Api\Model;

/**
 * Order line item type.
 */
final class LineItemType
{
    // Product physical, digital or services
    const Product = "product";
    // Fee shipping, handling or other fees
    const Fee = "fee";
    // Discount discounts
    const Discount = "discount";
}
