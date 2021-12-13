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
     * @param $payload
     * @param $signature
     * @param $secret
     * @return void
     * @throws \Exception
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
            case 'order.status.changed':
                $this->handleOrderStatusChangedEvent($this->denormalizer->denormalize($event['data'], OrderStatusChanged::class));
                break;
            default:
                $this->logger->warning(
                    sprintf('webhook event %s received but ignored', $event['type']),
                    array('payload' => $payload)
                );
        }
    }

    abstract protected function handleOrderStatusChangedEvent(OrderStatusChanged $model);
}
