<?php

namespace Fiizy\Api;

use Fiizy\Http\CurlHttpClient;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class MockHttpClient extends CurlHttpClient
{
    private $queue = [];

    public function sendRequest(RequestInterface $request)
    {
        if (!$this->queue) {
            throw new \OutOfBoundsException('Mock queue is empty');
        }

        return array_shift($this->queue);
    }

    public function append()
    {
        foreach (func_get_args() as $value) {
            if ($value instanceof ResponseInterface
                || $value instanceof \Exception
                || is_callable($value)
            ) {
                $this->queue[] = $value;
            } else {
                throw new \InvalidArgumentException('Expected a response or '
                    . 'exception. Found ' . \GuzzleHttp\describe_type($value));
            }
        }
    }
}
