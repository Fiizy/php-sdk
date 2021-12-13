<?php

namespace Fiizy\Http\Curl;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class StringStreamTest extends TestCase
{
    public function test_constructor()
    {
        $stream = new StringStream('Hello world!');
        Assert::assertEquals('Hello world!', $stream);
    }

    public function test_get_contents_from_start()
    {
        $stream = new StringStream('Hello world!');
        Assert::assertEquals('Hello world!', $stream->getContents());
    }

    public function test_get_contents_from_seek()
    {
        $stream = new StringStream('Hello world!');
        $stream->seek(6);
        Assert::assertEquals("world!", $stream->getContents());
    }

    public function test_get_size()
    {
        $stream = new StringStream('Hello world!');
        Assert::assertEquals(12, $stream->getSize());
    }

    public function test_tell()
    {
        $stream = new StringStream('Hello world!');
        Assert::assertEquals(0, $stream->tell());
    }

    public function test_tell_seek()
    {
        $stream = new StringStream('Hello world!');
        $stream->seek(6);
        Assert::assertEquals(6, $stream->tell());
    }

    public function test_eof()
    {
        $stream = new StringStream('Hello world!');
        Assert::assertEquals(false, $stream->eof());
    }

    public function test_eof_seek()
    {
        $stream = new StringStream('Hello world!');
        $stream->seek(12);
        Assert::assertEquals(true, $stream->eof());
    }

    public function test_seek_set()
    {
        $stream = new StringStream('Hello world!');
        $stream->seek(6);
        Assert::assertEquals(6, $stream->tell());
    }

    public function test_seek_cur()
    {
        $stream = new StringStream('Hello world!');
        $stream->seek(2);
        $stream->seek(6, SEEK_CUR);
        Assert::assertEquals(8, $stream->tell());
    }

    public function test_seek_end()
    {
        $stream = new StringStream('Hello world!');
        $stream->seek(6, SEEK_END);
        Assert::assertEquals(18, $stream->tell());
    }

    public function test_rewind()
    {
        $stream = new StringStream('Hello world!');
        $stream->seek(12);
        $stream->rewind();
        Assert::assertEquals(0, $stream->tell());
    }

    public function test_write_start()
    {
        $stream = new StringStream('Hello world!');
        $stream->write('Holla');
        $stream->rewind();
        Assert::assertEquals('Holla world!', $stream->getContents());
        Assert::assertEquals(12, $stream->getSize());
    }

    public function test_write_middle()
    {
        $stream = new StringStream('Hello world!');
        $stream->seek(6);
        $stream->write('Bobbi');
        $stream->rewind();
        Assert::assertEquals('Hello Bobbi!', $stream->getContents());
        Assert::assertEquals(12, $stream->getSize());
    }

    public function test_read_full()
    {
        $stream = new StringStream('Hello world!');
        Assert::assertEquals('Hello world!', $stream->read($stream->getSize()));
    }

    public function test_read_start()
    {
        $stream = new StringStream('Hello world!');
        Assert::assertEquals('Hello', $stream->read(5));
    }

    public function test_read_multiple()
    {
        $stream = new StringStream('Hello world!');
        $stream->read(6);
        Assert::assertEquals('world!', $stream->read(6));
    }
}
