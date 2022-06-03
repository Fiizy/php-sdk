<?php

namespace Fiizy\Api;

use Fiizy\Http\Curl\Response;
use Fiizy\Serializer\Normalizer\ObjectNormalizer;
use Fiizy\Serializer\SimpleSerializer;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;

abstract class ApiTestCase extends TestCase
{
    /**
     * @return MockHttpClient
     */
    protected function mockHttpClient()
    {
        return new MockHttpClient('Mock');
    }

    protected function mockResponse($status = 200, array $headers = [], $body = null)
    {
        return new Response($status, $headers, $body);
    }

    protected function createNormalizer()
    {
        return new ObjectNormalizer();
    }

    protected function createSerializer()
    {
        return new SimpleSerializer([$this->createNormalizer()]);
    }

    /**
     * @return Client
     */
    protected function createClient($httpClient, CacheInterface $cache = null)
    {
        $client = new Client("http://localhost");
        $client->setSerializer($this->createSerializer());
        $client->setDenormalizer($this->createNormalizer());
        $client->setHttpClient($httpClient);

        if ($cache !== null) {
            $client->setCache($cache);
        }

        return $client;
    }
}
