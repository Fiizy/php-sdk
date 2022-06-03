<?php

namespace Fiizy\Api;

use DateTime;
use DateTimeZone;
use Exception;
use Fiizy\Api\Exception\ApiClientException;
use Fiizy\Api\Exception\ApiCommunicationException;
use Fiizy\Api\Exception\ApiServerException;
use Fiizy\Api\Model\ResponseEnvelope;
use Fiizy\Http\ClientExceptionInterface;
use Fiizy\Http\HttpClientInterface;
use Fiizy\Serializer\Normalizer\DenormalizerInterface;
use Fiizy\Serializer\SerializerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * API Client.
 */
class Client
{
    const API_URL = 'https://api.fiizy.es';
    const BASE_PATH = 'api/v2/';
    const CACHE_KEY_PATTERN = 'fiizy-api-%s';

    /** @var SerializerInterface */
    protected $serializer;

    /** @var DenormalizerInterface */
    protected $denormalizer;

    /** @var HttpClientInterface */
    protected $httpClient;

    /** @var CacheInterface cache */
    protected $cache;

    /** @var string */
    private $apiUrl;

    /** @var string */
    private $apiBasePath;

    /** @var string api public key */
    private $publicKey;

    /** @var string api private key */
    private $privateKey;

    public function __construct($apiUrl = null, $apiBaseUri = null)
    {
        $this->apiUrl = $apiUrl ?: self::API_URL;
        $this->apiBasePath = $apiBaseUri ?: self::BASE_PATH;
    }

    /**
     * Set data serializer.
     *
     * @param SerializerInterface $serializer
     * @return void
     */
    public function setSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Set data denormalizer.
     *
     * @param DenormalizerInterface $denormalizer
     * @return void
     */
    public function setDenormalizer(DenormalizerInterface $denormalizer)
    {
        $this->denormalizer = $denormalizer;
    }

    /**
     * Set http client.
     *
     * @param HttpClientInterface $httpClient
     * @return void
     */
    public function setHttpClient(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Set cache.
     *
     * @param CacheInterface $cache
     * @return void
     */
    public function setCache(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Set api authorization keys
     *
     * @param string $publicKey api public key
     * @param string $privateKey api private key
     *
     * @return $this
     */
    public function setAuthorizationKeys($publicKey, $privateKey)
    {
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;

        return $this;
    }

    /**
     * Performs a GET request.
     *
     * @param string $path The path of the request.
     * @param array $query The filters of the request.
     * @param null|string $type Response class type.
     * @param boolean $verify Verify response signature.
     * @param boolean $cache whether result should be cached or not, if yes then first will check if cache exists
     * @param string|null $cacheKey specified string will be used as cache key, if omitted then key will be generated based on request
     *
     * @return mixed The data of the response.
     * @throws Exception
     */
    public function get($path, $query = null, $type = null, $verify = false, $cache = false, $cacheKey = null)
    {
        if ($cacheKey === null) {
            $cacheKey = base64_encode($path . serialize($query));
        }

        if ($cache === true && null !== ($data = $this->fromCache($cacheKey))) {
            return $data;
        }

        $response = $this->request('GET', $path, $query);
        $data = $this->deserialize($response, $type, $verify);
        $this->toCache($cacheKey, $data, $response, $cache);

        return $data;
    }

    /**
     * Performs a POST request.
     *
     * @param string $path The path of the request.
     * @param array|object $data The data of the request.
     * @param null|string $type Response class type.
     * @param boolean $verify Verify response signature.
     *
     * @return mixed The data of the response.
     * @throws Exception
     */
    public function post($path, $data, $type = null, $verify = false)
    {
        $response = $this->request('POST', $path, null, $data);
        return $this->deserialize($response, $type, $verify);
    }

    /**
     * Performs a PUT request.
     *
     * @param string $path The path of the request.
     * @param array|object $data The data of the request.
     * @param null|string $type Response class type.
     * @param boolean $verify Verify response signature.
     *
     * @return mixed The data of the response.
     * @throws Exception
     */
    public function put($path, $data, $type = null, $verify = false)
    {
        $response = $this->request('PUT', $path, null, $data);
        return $this->deserialize($response, $type, $verify);
    }

    /**
     * Performs a PATCH request.
     *
     * @param string $path The path of the request.
     * @param array|object $data The data of the request.
     * @param null|string $type Response class type.
     * @param boolean $verify Verify response signature.
     *
     * @return mixed The data of the response.
     * @throws Exception
     */
    public function patch($path, $data, $type = null, $verify = false)
    {
        $response = $this->request('PATCH', $path, null, $data);
        return $this->deserialize($response, $type, $verify);
    }

    /**
     * Performs a DELETE request.
     *
     * @param string $path The path of the request.
     * @param null|string $type Response class type.
     * @param boolean $verify Verify response signature.
     *
     * @return mixed The data of the response.
     * @throws Exception
     */
    public function delete($path, $type = null, $verify = false)
    {
        $response = $this->request('DELETE', $path);
        return $this->deserialize($response, $type, $verify);
    }

    /**
     * Performs a GET request and returns body.
     * If cacheable flag is set to true then response body will be cached and returned on subsequent requests.
     * If response contains Expires header then cache ttl will be set to specified value.
     *
     * @param string $uri uri to make request to
     * @param boolean $cache whether result should be cached or not, if yes then first will check if cache exists
     * @param string|null $cacheKey specified string will be used as cache key, if omitted then key will be generated based on request
     *
     * @return string
     * @throws Exception
     */
    public function fetch($uri, $cache = false, $cacheKey = null)
    {
        if ($cacheKey === null) {
            $cacheKey = base64_encode($uri);
        }

        if ($cache === true && null !== ($body = $this->fromCache($cacheKey))) {
            return $body;
        }

        $request = $this->httpClient
            ->createRequest('GET', $uri);

        try {
            $response = $this->httpClient->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw ApiCommunicationException::fromException($e);
        }

        if ($response->getStatusCode() >= 500) {
            throw ApiServerException::fromResponse($response);
        }

        if ($response->getStatusCode() >= 400) {
            throw ApiClientException::fromResponse($response);
        }

        if (empty($response->getBody())) {
            throw new \Exception('empty response');
        }

        $body = (string) $response->getBody();
        $this->toCache($cacheKey, $body, $response, $cache);

        return $body;
    }

    /**
     * Performs a request.
     *
     * @param string $method The HTTP method of the request.
     * @param string $path The path of the request.
     * @param array|null $query The query parameters of the request.
     * @param array|object|null $data The data of the request.
     *
     * @return ResponseInterface The data of the response.
     */
    protected function request($method, $path, $query = null, $data = null)
    {
        $uri = $this->httpClient
            ->createUri($this->apiUrl)
            ->withPath(rtrim($this->apiBasePath, '/') . '/' . ltrim($path, '/'));

        if ($query != null) {
            $uri = $uri->withQuery(http_build_query($query));
        }

        $request = $this->httpClient
            ->createRequest($method, $uri);

        if (!empty($this->publicKey) && !empty($this->privateKey)) {
            $request = $request->withHeader('Authorization', sprintf('Basic %s', base64_encode($this->publicKey . ':' . $this->privateKey)));
        }

        if ($data != null) {
            $body = $this->httpClient
                ->createStream($this->serializer->serialize($data));

            $request = $request
                ->withBody($body)
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('Content-Length', $body->getSize());
        }

        try {
            $response = $this->httpClient->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw ApiCommunicationException::fromException($e);
        }

        if ($response->getStatusCode() >= 500) {
            throw ApiServerException::fromResponse($response);
        }

        if ($response->getStatusCode() >= 400) {
            throw ApiClientException::fromResponse($response);
        }

        return $response;
    }

    /**
     * Deserialize response data into target object.
     *
     * @param ResponseInterface $response the response
     * @param null|string $target the response data target object
     * @param false $verify verify response data
     * @return mixed
     * @throws Exception
     */
    protected function deserialize(ResponseInterface $response, $target = null, $verify = false)
    {
        if (null === $target) {
            return null;
        }

        if (empty($response->getBody())) {
            throw new Exception('empty response body');
        }

        $result = $this->serializer->deserialize((string) $response->getBody(), ResponseEnvelope::class);

        if (!$result->success) {
            throw new ApiClientException(400, 'request failed', $result->errors);
        }

        if (!isset($result->data)) {
            throw new Exception(
                sprintf('failed to decode response, missing data, response: %s', $response->getBody())
            );
        }

        if ($verify && !$this->verify($response, $result)) {
            throw new Exception('invalid signature');
        }

        return $this->denormalizer->denormalize($result->data, $target);
    }

    /**
     * Verify response data signature.
     *
     * @param ResponseInterface $response response
     * @param ResponseEnvelope $envelope parsed response
     * @return bool
     * @throws Exception
     */
    protected function verify(ResponseInterface $response, ResponseEnvelope $envelope)
    {
        if (empty($response->getHeaderLine(Util\Signature::HEADER_KEY))) {
            throw new Exception('missing signature');
        }

        $payload = $this->serializer->serialize($envelope->data);

        if (!$payload) {
            throw new Exception('failed to encode data');
        }

        return Util\Signature::verifyHeader(
            $this->privateKey,
            $response->getHeaderLine(Util\Signature::HEADER_KEY),
            $payload,
            Util\Signature::DEFAULT_DIFFERENCE
        );
    }

    /**
     * Format cache key.
     *
     * @param string $key cache key
     * @return string
     */
    protected function formatCacheKey($key)
    {
        // limit key length to sensible value and add prefix
        return sprintf(self::CACHE_KEY_PATTERN, substr($key, 0, 8));
    }

    /**
     * Get value from cache.
     *
     * @param string $key cache key
     * @return mixed|null
     */
    protected function fromCache($key)
    {
        if ($this->cache === null) {
            return null;
        }

        $key = $this->formatCacheKey($key);

        try {
            return $this->cache->get($key);
        } catch (\Psr\SimpleCache\InvalidArgumentException $e) {
            return null;
        }
    }

    /**
     * Add value to cache.
     *
     * @param string $key cache key
     * @param mixed $value value to cache
     * @param ResponseInterface $response the HTTP response this value has been obtained from
     * @param bool $cache should we cache or flush the cache
     * @return void
     */
    protected function toCache($key, $value, $response, $cache)
    {
        if ($this->cache === null) {
            return;
        }

        $key = $this->formatCacheKey($key);

        try {
            if ($cache === true) {
                $ttl = null;

                try {
                    if (
                        $response->hasHeader('Expires')
                        && false !== $expires = DateTime::createFromFormat(\DATE_RFC2822, $response->getHeaderLine('Expires'))
                    ) {
                        $ttl = $expires->diff(new DateTime('now', new DateTimeZone('UTC')));
                    }
                } catch (\Exception $e) {
                    // date error should not prevent code from completion
                }

                $this->cache->set($key, $value, $ttl);
            } else {
                $this->cache->delete($key);
            }
        } catch (\Psr\SimpleCache\InvalidArgumentException $e) {
            // cache error should not prevent code from completion
        }
    }
}
