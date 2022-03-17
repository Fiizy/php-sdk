<?php

namespace Fiizy\Api\Webhook\Model;

/**
 * Order status.
 */
final class OrderStatus
{
    const Pending = "pending";
    const Validating = "validating";
    const Approved = "approved";
    const Canceled = "canceled";
}
