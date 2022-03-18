<?php

namespace Fiizy\Serializer\Normalizer;

/**
 * Object normalizer interface.
 */
interface NormalizerInterface
{
    /**
     * Normalizes an object.
     *
     * @param mixed  $object  Object to normalize
     * @return array
     */
    public function normalize($object);
}
