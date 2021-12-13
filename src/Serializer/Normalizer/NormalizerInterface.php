<?php

namespace Fiizy\Serializer\Normalizer;

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
