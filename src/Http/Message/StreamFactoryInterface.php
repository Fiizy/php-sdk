<?php

namespace Fiizy\Http\Message;

use Psr\Http\Message\StreamInterface;

/**
 * Stream factory interface based on PSR-17: HTTP Factories.
 */
interface StreamFactoryInterface
{
    /**
     * Create a new stream from a string.
     *
     * The stream SHOULD be created with a temporary resource.
     *
     * @param string $content String content with which to populate the stream.
     *
     * @return StreamInterface
     */
    public function createStream($content = '');
}
