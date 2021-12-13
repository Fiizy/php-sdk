<?php

namespace Fiizy\Serializer;

use Fiizy\Serializer\Normalizer\DenormalizerInterface;
use Fiizy\Serializer\Normalizer\NormalizerInterface;

/**
 * Simple json serializer.
 */
class SimpleSerializer implements SerializerInterface
{
    /** @var NormalizerInterface */
    protected $normalizer;

    /** @var DenormalizerInterface */
    protected $denormalizer;

    public function __construct(array $normalizers)
    {
        foreach ($normalizers as $normalizer) {
            if ($normalizer instanceof NormalizerInterface) {
                $this->normalizer = $normalizer;
            }
            if ($normalizer instanceof DenormalizerInterface) {
                $this->denormalizer = $normalizer;
            }
        }
    }

    public function serialize($data)
    {
        if (is_object($data)) {
            $data = $this->normalizer->normalize($data);
        }

        return json_encode($data, \JSON_PRESERVE_ZERO_FRACTION);
    }

    public function deserialize($data, $type = null)
    {
        $decoded = json_decode($data, true);

        if (null === $type) {
            return $decoded;
        }

        return $this->denormalizer->denormalize($decoded, $type);
    }
}
