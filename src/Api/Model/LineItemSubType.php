<?php

namespace Fiizy\Api\Model;

/**
 * Order line item sub type.
 */
final class LineItemSubType
{
    // Physical product - is a tangible item that can be shipped with proof of delivery
    const PhysicalProduct = "physical";
    // Digital product - items that are stored, delivered, and used in their electronic format
    const DigitalProduct = "digital";
    // Service product - intangible items, ex: hairstyling, car service, repairs
    const ServiceProduct = "service";

    const ShippingFee = "shipping";
    const HandlingFee = "handling";
    const WrappingFee = "wrapping";

    const DiscountDiscount = "discount";
}
