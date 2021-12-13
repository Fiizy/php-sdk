<?php

namespace Fiizy\Serializer\Normalizer;

interface DenormalizerInterface
{
    /**
     * Denormalizes data back into an object of the given class.
     *
     * @param mixed  $data    Data to restore
     * @param string $type    The expected class to instantiate
     * @return mixed
     */
    public function denormalize($data, $type);
}
