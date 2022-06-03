<?php

namespace Fiizy\Api;

use Psr\SimpleCache\CacheInterface;

class MockCache implements CacheInterface
{
    public $cache = [];
    public $ttl = [];

    public function get($key, $default = null)
    {
        return (isset($this->cache[$key]) ? $this->cache[$key] : $default);
    }

    public function set($key, $value, $ttl = null)
    {
        $this->cache[$key] = $value;
        $this->ttl[$key] = $ttl;
    }

    public function delete($key)
    {
    }

    public function clear()
    {
    }

    public function getMultiple($keys, $default = null)
    {
    }

    public function setMultiple($values, $ttl = null)
    {
    }

    public function deleteMultiple($keys)
    {
    }

    public function has($key)
    {
    }
}
