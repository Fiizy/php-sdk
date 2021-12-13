<?php

namespace Fiizy\Http\Curl;

use Fiizy\Http\ClientInterface;
use Psr\Http\Message\RequestInterface;

/**
 * Curl HTTP client implementation.
 */
class Client implements ClientInterface
{
    protected $userAgent;

    /**
     * Create a new curl client with provided user agent header.
     *
     * @param $userAgent
     */
    public function __construct($userAgent)
    {
        $this->userAgent = $userAgent;
    }

    public function sendRequest(RequestInterface $request)
    {
        $resource = curl_init();

        if (false === $resource) {
            throw new \Exception('failed to setup curl');
        }

        curl_setopt($resource, CURLOPT_URL, $request->getUri());
        curl_setopt($resource, CURLOPT_CUSTOMREQUEST, $request->getMethod());

        $headers = array();
        $headers[] = sprintf('User-Agent: %s', $this->userAgent);

        foreach ($request->getHeaders() as $header => $value) {
            $headers[] = sprintf('%s:%s', $header, implode(', ', $value));
        }

        curl_setopt($resource, CURLOPT_HTTPHEADER, $headers);

        $body = $request->getBody();

        if (!empty($body)) {
            curl_setopt($resource, CURLOPT_POSTFIELDS, $body->getContents());
        }

        curl_setopt($resource, CURLOPT_HEADER, true);
        curl_setopt($resource, CURLOPT_TIMEOUT, 30);
        curl_setopt($resource, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($resource);
        $error = curl_error($resource);

        if (!empty($error)) {
            curl_close($resource);

            throw new \Exception($error);
        }

        $headerSize = curl_getinfo($resource, CURLINFO_HEADER_SIZE);
        $responseHeaders = $this->headerToArray(substr($response, 0, $headerSize));
        $responseBody = substr($response, $headerSize);

        return new Response(
            curl_getinfo($resource, CURLINFO_HTTP_CODE),
            $responseHeaders,
            $responseBody
        );
    }

    /**
     * Parse header text to array.
     *
     * @param string $text
     *
     * @return array<string, mixed>
     */
    protected function headerToArray($text)
    {
        $lines = explode("\r\n", trim($text));
        $headers = [];

        foreach ($lines as $line) {
            // skip empty lines
            if (!$line) {
                continue;
            }

            // skip lines that dont have delimiter
            $delimiter = strpos($line, ':');
            if (!$delimiter) {
                continue;
            }

            $key = trim(substr($line, 0, $delimiter));
            $val = ltrim(substr($line, $delimiter + 1));

            if (!isset($headers[$key])) {
                $headers[$key] = $val;
            } elseif (is_array($headers[$key])) {
                $headers[$key][] = $val;
            } else {
                $headers[$key] = [$headers[$key], $val];
            }
        }

        return $headers;
    }
}
