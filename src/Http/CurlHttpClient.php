<?php

namespace Fiizy\Http;

use Fiizy\Http\Curl\Client;
use Fiizy\Http\Curl\Request;
use Fiizy\Http\Curl\StringStream;
use Fiizy\Http\Curl\Uri;

/**
 * Curl HTTP client.
 */
class CurlHttpClient extends Client implements HttpClientInterface
{
    public function createRequest($method, $uri)
    {
        return new Request($method, $uri);
    }

    public function createStream($content = '')
    {
        return new StringStream($content);
    }

    public function createUri($uri = '')
    {
        return new Uri($uri);
    }
}
