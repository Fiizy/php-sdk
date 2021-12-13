<?php

namespace Fiizy\Api\Model;

/**
 * Order status.
 */
final class OrderStatus
{
    // New newly created order
    const NewOrder = "new";
    // ReadyToShip all order items are in stock and ready be shipped
    const ReadyToShip = "ready_to_ship";
    // Shipped all order items are shipped
    const Shipped = "shipped";
    // Delivered all order items are delivered
    const Delivered = "delivered";
    // Canceled order canceled
    const Canceled = "canceled";
}
