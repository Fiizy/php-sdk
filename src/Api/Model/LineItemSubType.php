<?php

namespace Fiizy\Api\Model;

/**
 * Order line item sub type.
 */
final class LineItemSubType
{
    // Physical product - is a tangible item that can be shipped with proof of delivery
    public const PhysicalProduct = "physical";
    // Digital product - items that are stored, delivered, and used in their electronic format
    public const DigitalProduct = "digital";
    // Service product - intangible items, ex: hairstyling, car service, repairs
    public const ServiceProduct = "service";

    public const ShippingFee = "shipping";
    public const HandlingFee = "handling";
    public const WrappingFee = "wrapping";

    public const DiscountDiscount = "discount";
}
