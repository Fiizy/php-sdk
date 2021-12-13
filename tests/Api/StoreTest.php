<?php

namespace Fiizy\Api;

use Fiizy\Api\Model\StoreStatusRequest;
use PHPUnit\Framework\Assert;

class StoreTest extends ApiTestCase
{
    public function test_inactive_store()
    {
        $mock = $this->mockHttpClient();
        $mock->append($this->mockResponse(200, [], '{
            "success":true,
            "data":{
                "status_code":"inactive",
                "status_label":"Not started"
            }
        }'));

        $client = $this->createClient($mock);
        $client = $client->setAuthorizationKeys("public", "private");

        $api = new Store($client);
        $response = $api->status(new StoreStatusRequest());

        $expected = new \Fiizy\Api\Model\StoreStatusResponse();
        $expected->statusCode = 'inactive';
        $expected->statusLabel = 'Not started';

        Assert::assertEquals($expected, $response);
    }
}
