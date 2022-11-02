<?php

namespace Fiizy\Api\Webhook;

use Fiizy\Api\Util\Signature;
use Fiizy\Api\Webhook\Model\OrderStatusChanged;
use Fiizy\Serializer\Normalizer\DenormalizerInterface;
use Fiizy\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;

/**
 * Abstract webhook notification receiver, validates payload signature and triggers appropriate callback.
 */
abstract class Receiver
{
    /** @varLoggerInterface */
    protected $logger;

    /** @var SerializerInterface */
    protected $serializer;

    /** @var DenormalizerInterface */
    protected $denormalizer;

    /**
     * @param LoggerInterface $logger
     * @param DenormalizerInterface $denormalizer
     */
    public function __construct(LoggerInterface $logger, SerializerInterface $serializer, DenormalizerInterface $denormalizer)
    {
        $this->logger = $logger;
        $this->serializer = $serializer;
        $this->denormalizer = $denormalizer;
    }

    /**
     * Process received webhook payload.
     *
     * @param string $payload Event payload
     * @param string $signature Event signature
     * @param string $secret Secret key used to verify signature
     * @return boolean True if event processed and caused store data to change, false if store data left as is.
     * @throws \Exception Throws exception if fails to process event.
     */
    public function process($payload, $signature, $secret)
    {
        if (!Signature::verifyHeader(
            $secret,
            $signature,
            $payload,
            Signature::DEFAULT_DIFFERENCE
        )) {
            throw new \Exception('invalid signature', 401);
        }

        $event = $this->serializer->deserialize($payload);

        switch ($event['type']) {
            case 'store.update':
                return $this->handleStoreUpdateEvent();
            case 'order.status.changed':
                return $this->handleOrderStatusChangedEvent($this->denormalizer->denormalize($event['data'], OrderStatusChanged::class));
            default:
                $this->logger->warning(
                    sprintf('webhook event %s received but ignored', $event['type']),
                    array('payload' => $payload)
                );
                throw new \Exception('unsupported event type', 304);
        }
    }

    /**
     * Handle store update event.
     *
     * @return boolean True if event processed and caused store data to change, false if store data left as is.
     * @throws \Exception Throws exception if fails to handle event.
     */
    abstract protected function handleStoreUpdateEvent();

    /**
     * Handle order status change event.
     *
     * @param OrderStatusChanged $model Order status change event object.
     * @return boolean True if event processed and caused store data to change, false if store data left as is.
     * @throws \Exception Throws exception if fails to handle event.
     */
    abstract protected function handleOrderStatusChangedEvent(OrderStatusChanged $model);
}
