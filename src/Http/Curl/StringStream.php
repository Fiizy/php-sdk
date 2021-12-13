<?php

namespace Fiizy\Http\Curl;

use Psr\Http\Message\StreamInterface;

/**
 * PSR-7 Stream for string content type.
 */
class StringStream implements StreamInterface
{
    /** @var string|null */
    private $content;

    /** @var int */
    private $pointer = 0;

    /** @var int */
    private $length;

    public function __construct($content)
    {
        if (!is_string($content)) {
            throw new \InvalidArgumentException('Only string supported');
        }

        $this->content = $content;
        $this->length = strlen($content);
    }

    public function __toString()
    {
        try {
            return $this->getContents();
        } catch (\Exception $e) {
            return '';
        }
    }

    public function close()
    {
        $this->content = null;
        $this->pointer = 0;
        $this->length  = 0;
    }

    public function detach()
    {
        if (!isset($this->content)) {
            return null;
        }

        $result = $this->content;

        unset($this->content);
        unset($this->pointer);
        unset($this->length);

        return $result;
    }

    public function getSize()
    {
        return $this->length;
    }

    public function tell()
    {
        return $this->pointer;
    }

    public function eof()
    {
        return $this->pointer === $this->length;
    }

    public function isSeekable()
    {
        return $this->content !== null;
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        switch ($whence) {
            case SEEK_SET:
                $this->pointer = $offset;
                return;
            case SEEK_CUR:
                $this->pointer = $this->pointer + $offset;
                return;
            case SEEK_END:
                $this->pointer = $this->length + $offset;
                return;
        }
    }

    public function rewind()
    {
        $this->pointer = 0;
    }

    public function isWritable()
    {
        return $this->content !== null;
    }

    public function write($string)
    {
        $length = strlen($string);
        $offset = $this->pointer + $length;
        $this->content = substr($this->content, 0, $this->pointer) . $string . substr($this->content, $offset);
        $this->length  = strlen($this->content);
        $this->pointer = $this->length;

        return $length;
    }

    public function isReadable()
    {
        return $this->content !== null;
    }

    public function read($length)
    {
        $sub = substr($this->content, $this->pointer, $length);
        $this->pointer += $length;

        return $sub;
    }

    public function getContents()
    {
        return $this->read($this->length - $this->pointer);
    }

    public function getMetadata($key = null)
    {
        return null;
    }
}
