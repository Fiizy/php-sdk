<?php

namespace Fiizy\Api\Model;

/**
 * Order status.
 */
final class OrderStatus
{
    // New newly created order
    public const NewOrder = "new";
    // Validating order
    public const Validating  = "validating";
    // Approved order payment is approved
    public const Approved  = "approved";
    // ReadyToShip all order items are in stock and ready be shipped
    public const ReadyToShip = "ready_to_ship";
    // Shipped all order items are shipped
    public const Shipped = "shipped";
    // Delivered all order items are delivered
    public const Delivered = "delivered";
    // Canceled order canceled
    public const Canceled = "canceled";
}
