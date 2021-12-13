<?php

namespace Fiizy\Api;

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

/**
 * API Client.
 */
class Client
{
    const API_URL = 'https://api.fiizy.es';
    const BASE_PATH = 'api/v2/';

    /** @var SerializerInterface */
    protected $serializer;

    /** @var DenormalizerInterface */
    protected $denormalizer;

    /** @var HttpClientInterface */
    protected $httpClient;

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
     *
     * @return mixed The data of the response.
     * @throws Exception
     */
    public function get($path, $query = null, $type = null, $verify = false)
    {
        $response = $this->request('GET', $path, $query);
        return $this->deserialize($response, $type, $verify);
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
}
