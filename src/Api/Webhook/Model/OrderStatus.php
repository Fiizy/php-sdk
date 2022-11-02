<?php

namespace Fiizy\Api\Webhook\Model;

/**
 * Order status.
 */
final class OrderStatus
{
    public const Pending = "pending";
    public const Validating = "validating";
    public const Approved = "approved";
    public const Canceled = "canceled";
}
