<?php

namespace Fiizy\Http\Curl;

use Psr\Http\Message\UriInterface;

/**
 * PSR-7 uri interface implementation.
 */
class Uri implements UriInterface
{
    /** @var string Uri scheme. */
    private $scheme = '';

    /** @var string Uri user info. */
    private $userInfo = '';

    /** @var string Uri host. */
    private $host = '';

    /** @var int|null Uri port. */
    private $port;

    /** @var string Uri path. */
    private $path = '';

    /** @var string Uri query string. */
    private $query = '';

    /** @var string Uri fragment. */
    private $fragment = '';

    public function __construct($uri = '')
    {
        if ($uri != '') {
            $parts = parse_url($uri);

            if ($parts !== false) {
                $this->scheme = isset($parts['scheme']) ? $parts['scheme'] : '';
                $this->host = isset($parts['host']) ? $parts['host'] : '';
                $this->port = isset($parts['port']) ? $parts['port'] : null;
                $this->path = isset($parts['path']) ? '/' . ltrim($parts['path'], '/') : '';
                $this->query = isset($parts['query']) ? $parts['query'] : '';
                $this->fragment = isset($parts['fragment']) ? $parts['fragment'] : '';
                $this->userInfo = isset($parts['user']) ? $parts['user'] : '';

                if (isset($parts['pass'])) {
                    $this->userInfo .= ':' . $parts['pass'];
                }
            }
        }
    }

    public function getScheme()
    {
        return $this->scheme;
    }

    public function getAuthority()
    {
        $authority = $this->host;

        if ($this->userInfo !== '') {
            $authority = $this->userInfo . '@' . $authority;
        }

        if ($this->port !== null) {
            $authority .= ':' . $this->port;
        }

        return $authority;
    }

    public function getUserInfo()
    {
        return $this->userInfo;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getFragment()
    {
        return $this->fragment;
    }

    public function withScheme($scheme)
    {
        $new = clone $this;
        $new->scheme = $scheme;
        return $new;
    }

    public function withUserInfo($user, $password = null)
    {
        $info = $user;

        if ($password !== null) {
            $info .= ':' . $password;
        }

        if ($this->userInfo === $info) {
            return $this;
        }

        $new = clone $this;
        $new->userInfo = $info;
        return $new;
    }

    public function withHost($host)
    {
        if ($this->host === $host) {
            return $this;
        }

        $new = clone $this;
        $new->host = $host;
        return $new;
    }

    public function withPort($port)
    {
        if ($this->port === $port) {
            return $this;
        }

        $new = clone $this;
        $new->port = $port;
        return $new;
    }

    public function withPath($path)
    {
        if ($this->path === $path) {
            return $this;
        }

        $new = clone $this;
        $new->path = '/' . ltrim($path, '/');
        return $new;
    }

    public function withQuery($query)
    {
        if ($this->query === $query) {
            return $this;
        }

        $new = clone $this;
        $new->query = $query;
        return $new;
    }

    public function withFragment($fragment)
    {
        if ($this->fragment === $fragment) {
            return $this;
        }

        $new = clone $this;
        $new->fragment = $fragment;
        return $new;
    }

    public function __toString()
    {
        $uri = '';

        if ($this->scheme != '') {
            $uri .= $this->scheme . ':';
        }

        $authority = $this->getAuthority();

        if ($authority != ''|| $this->scheme === 'file') {
            $uri .= '//' . $authority;
        }

        $uri .= $this->path;

        if ($this->query != '') {
            $uri .= '?' . $this->query;
        }

        if ($this->fragment != '') {
            $uri .= '#' . $this->fragment;
        }

        return $uri;
    }
}
