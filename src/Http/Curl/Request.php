<?php

namespace Fiizy\Http\Curl;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * PSR-7 request implementation.
 */
class Request extends AbstractMessage implements RequestInterface
{
    const HOST_HEADER_KEY = 'Host';

    /** @var string */
    private $method;

    /** @var string|null */
    private $requestTarget;

    /** @var UriInterface */
    private $uri;

    public function __construct($method, $uri)
    {
        $this->method = strtoupper($method);
        $this->uri = $uri;
    }

    public function getRequestTarget()
    {
        if ($this->requestTarget !== null) {
            return $this->requestTarget;
        }

        $target = $this->uri->getPath();

        if ($target == '') {
            $target = '/';
        }

        if ($this->uri->getQuery() != '') {
            $target .= '?' . $this->uri->getQuery();
        }

        return $target;
    }

    public function withRequestTarget($requestTarget)
    {
        $new = clone $this;
        $new->requestTarget = $requestTarget;
        return $new;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function withMethod($method)
    {
        $new = clone $this;
        $new->method = strtoupper($method);
        return $new;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $new = clone $this;
        $new->uri = $uri;

        if (!$preserveHost || !$this->hasHeader(self::HOST_HEADER_KEY)) {
            $host = $this->uri->getHost();

            if ($host != '') {
                if (($port = $this->uri->getPort()) !== null) {
                    $host .= ':' . $port;
                }

                $this->headers = [self::HOST_HEADER_KEY => [$host]] + $this->headers;
            }
        }

        return $new;
    }
}
