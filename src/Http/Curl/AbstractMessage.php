<?php

namespace Fiizy\Http\Curl;

use Psr\Http\Message\StreamInterface;

abstract class AbstractMessage
{

    /** @var array Map of all registered headers, as original name => array of values */
    protected $headers = [];

    /** @var string */
    protected $protocol = '1.1';

    /** @var StreamInterface|null */
    protected $stream;

    public function getProtocolVersion()
    {
        return $this->protocol;
    }

    public function withProtocolVersion($version)
    {
        if ($this->protocol === $version) {
            return $this;
        }

        $new = clone $this;
        $new->protocol = $version;
        return $new;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function hasHeader($name)
    {
        $name = strtolower($name);

        foreach ($this->headers as $key => $value) {
            if ($name == strtolower($key)) {
                return true;
            }
        }

        return false;
    }

    public function getHeader($name)
    {
        $name = strtolower($name);

        foreach ($this->headers as $key => $value) {
            if ($name == strtolower($key)) {
                return $value;
            }
        }

        return [];
    }

    public function getHeaderLine($name)
    {
        return implode(', ', $this->getHeader($name));
    }

    public function withHeader($name, $value)
    {
        $new = clone $this;
        $new->headers[$name] = $this->normalizeHeaderValue($value);
        return $new;
    }

    public function withAddedHeader($name, $value)
    {
        $new = clone $this;
        $new->setHeaders([$name => $value]);
        return $new;
    }

    public function withoutHeader($name)
    {
        if (!$this->hasHeader($name)) {
            return $this;
        }

        $name = strtolower($name);

        $new = clone $this;

        foreach ($new->headers as $key => $value) {
            if ($name == strtolower($key)) {
                unset($new->headers[$key]);
            }
        }

        return $new;
    }

    public function getBody()
    {
        return $this->stream;
    }

    public function withBody(StreamInterface $body)
    {
        if ($body === $this->stream) {
            return $this;
        }

        $new = clone $this;
        $new->stream = $body;
        return $new;
    }

    protected function setHeaders(array $headers)
    {
        foreach ($headers as $header => $value) {
            $value = $this->normalizeHeaderValue($value);

            if ($this->hasHeader($header)) {
                $this->headers[$header] = array_merge($this->headers[$header], $value);
            } else {
                $this->headers[$header] = $value;
            }
        }
    }

    protected function normalizeHeaderValue($value)
    {
        if (!is_array($value)) {
            return [$value];
        }

        if (count($value) === 0) {
            throw new \InvalidArgumentException('Header value can not be an empty array.');
        }

        return $value;
    }
}
