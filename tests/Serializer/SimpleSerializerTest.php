<?php

namespace Fiizy\Serializer;

use Fiizy\Api\Model\Address;
use Fiizy\Api\Model\LineItem;
use Fiizy\Api\Model\Order;
use Fiizy\Api\Model\StoreStatusResponse;
use Fiizy\Serializer\Normalizer\ObjectNormalizer;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class SimpleSerializerTest extends TestCase
{
    public function test_store_serialize_deserialize()
    {
        $data = new StoreStatusResponse();
        $data->statusCode = 'some-code';
        $data->statusLabel = 'some-label';

        $serializer = new SimpleSerializer(array(new ObjectNormalizer()));
        $json = $serializer->serialize($data);
        $dataCopy = $serializer->deserialize($json, StoreStatusResponse::class);

        Assert::assertEquals($data, $dataCopy);
    }

    public function test_order_serialize()
    {
        $item = new LineItem();
        $item->name = 'line-item-name-1';

        $data = new Order();
        $data->lineItems = array($item);

        $serializer = new SimpleSerializer(array(new ObjectNormalizer()));
        $json = $serializer->serialize($data);

        Assert::assertEquals('{"line_items":[{"name":"line-item-name-1"}]}', $json);
    }

    public function test_address_serialize()
    {
        $data = new Address();
        $data->addressLine1 = 'address-line-1';
        $data->addressLine2 = 'address-line-2';

        $serializer = new SimpleSerializer(array(new ObjectNormalizer()));
        $json = $serializer->serialize($data);

        Assert::assertEquals('{"address_line_1":"address-line-1","address_line_2":"address-line-2"}', $json);
    }
}
