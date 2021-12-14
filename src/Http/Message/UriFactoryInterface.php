<?php

namespace Fiizy\Http\Message;

use Psr\Http\Message\UriInterface;

/**
 * Uri factory interface based on PSR-17: HTTP Factories.
 */
interface UriFactoryInterface
{
    /**
     * Create a new URI.
     *
     * @param string $uri
     *
     * @return UriInterface
     *
     * @throws \InvalidArgumentException If the given URI cannot be parsed.
     */
    public function createUri($uri = '');
}
