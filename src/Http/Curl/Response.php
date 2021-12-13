<?php

namespace Fiizy\Http\Curl;

use Psr\Http\Message\ResponseInterface;

/**
 * PSR-7 response implementation.
 */
class Response extends AbstractMessage implements ResponseInterface
{

    /** @var string */
    private $reasonPhrase = '';

    /** @var int */
    private $statusCode = 200;

    public function __construct($status = 200, array $headers = [], $body = null)
    {
        $this->statusCode = $status;
        $this->setHeaders($headers);

        if (!empty($body)) {
            $this->stream = new StringStream($body);
        }
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        $new = clone $this;
        $new->statusCode = (int) $code;
        $new->reasonPhrase = (string) $reasonPhrase;
        return $new;
    }
}
