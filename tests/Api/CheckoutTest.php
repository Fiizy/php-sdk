<?php

namespace Fiizy\Api;

use Fiizy\Api\Exception\ApiClientException;
use PHPUnit\Framework\Assert;

class CheckoutTest extends ApiTestCase
{
    public function test_checkout_status()
    {
        $serializer = $this->createSerializer();

        $expected = new \Fiizy\Api\Model\StatusResponse();
        $expected->reference = 'reference-1';
        $expected->number = 'number-1';
        $expected->status = 'status-1';
        $expected->amount = 100.55;
        $expected->currency = 'EUR';
        $expected->metadata = array(
            'meta-key-1' => 'value-1'
        );

        $secret = "private";
        $timestamp = time();
        $data = $serializer->serialize($expected);
        $payload = "{$timestamp}.{$data}";
        $signature = hash_hmac('sha256', $payload, $secret);

        $mock = $this->mockHttpClient();
        $mock->append($this->mockResponse(200, [
            Util\Signature::HEADER_KEY => sprintf('t=%d, s=%s', $timestamp, $signature)
        ], sprintf('{
            "success":true,
            "data": %s
        }', $data)));

        $client = $this->createClient($mock);
        $client = $client->setAuthorizationKeys("public", $secret);

        $api = new Checkout($client);
        $response = $api->status("order-ref");

        Assert::assertEquals($expected, $response);
    }

    public function test_checkout_start_unauthorized()
    {
        $request = new \Fiizy\Api\Model\CheckoutRequest();

        $mock = $this->mockHttpClient();
        $mock->append($this->mockResponse(401, [], 'Unauthorized'));

        $client = $this->createClient($mock);
        $client = $client->setAuthorizationKeys("public", "private");

        $api = new Checkout($client);

        $this->expectException(ApiClientException::class);

        $api->start($request);
    }

    public function test_checkout_start_error()
    {
        $request = new \Fiizy\Api\Model\CheckoutRequest();

        $mock = $this->mockHttpClient();
        $mock->append($this->mockResponse(400, [], '{
            "success": false,
            "errors": [{"error": "order reference missing"}]
        }'));

        $client = $this->createClient($mock);
        $client = $client->setAuthorizationKeys("public", "private");

        $api = new Checkout($client);

        $this->expectException(ApiClientException::class);

        $api->start($request);
    }
}
