<?php

namespace Fiizy\Serializer\Normalizer;

/**
 * Object normalizer and de-normalizer.
 */
class ObjectNormalizer implements NormalizerInterface, DenormalizerInterface
{

    /**
     * Normalizes an object.
     * Convert object into an array of key value pairs, with keys converted to snake_case.
     *
     * @param $object
     * @return array
     */
    public function normalize($object)
    {
        $vars = get_object_vars($object);
        $data = array();

        foreach ($vars as $key => $value) {
            if (is_null($value)) {
                continue;
            }

            if (is_object($value)) {
                $value = $this->normalize($value);
            } elseif (is_array($value)) {
                foreach ($value as $k => $v) {
                    if (is_object($v)) {
                        $value[$this->normalizeName($k)] = $this->normalize($v);
                    }
                }
            }

            $data[$this->normalizeName($key)] = $value;
        }

        return $data;
    }

    /**
     * Denormalizes data back into an object of the given class.
     *
     * @param $data
     * @param $type
     * @return mixed
     */
    public function denormalize($data, $type)
    {
        $object = new $type();

        foreach ($data as $key => $value) {
            $property = $this->denormalizeName($key);

            if (property_exists($type, $property)) {
                $object->{$property} = $value;
            }
        }

        return $object;
    }

    /**
     * Convert property name from camelCase to snake_case.
     *
     * @param $propertyName
     * @return int|string
     */
    public function normalizeName($propertyName)
    {
        if (is_numeric($propertyName)) {
            return $propertyName;
        }

        return strtolower(preg_replace('/[A-Z0-9]/', '_\\0', lcfirst($propertyName)));
    }

    /**
     * Convert property name from Snake_case to camelCase.
     *
     * @param $propertyName
     * @return string
     */
    public function denormalizeName($propertyName)
    {
        return lcfirst(preg_replace_callback('/(^|_|\.)+(.)/', function ($match) {
            return ('.' === $match[1] ? '_' : '') . strtoupper($match[2]);
        }, $propertyName));
    }
}
