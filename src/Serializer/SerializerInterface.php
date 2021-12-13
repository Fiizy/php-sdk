<?php

namespace Fiizy\Serializer;

/**
 * Serializer interface.
 */
interface SerializerInterface
{
    /**
     * Serializes data in the appropriate format.
     *
     * @param mixed  $data    Any data
     *
     * @return string
     */
    public function serialize($data);

    /**
     * Deserializes data into the given type.
     *
     * @param mixed  $data   Any data
     * @param null|string $type   Type
     *
     * @return mixed
     */
    public function deserialize($data, $type = null);
}
