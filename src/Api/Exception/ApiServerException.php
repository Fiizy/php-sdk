<?php

namespace Fiizy\Api\Exception;

use Psr\Http\Message\ResponseInterface;
use RuntimeException;

/**
 * Exception thrown when received HTTP 5xx client errors.
 */
class ApiServerException extends RuntimeException implements ApiExceptionInterface
{
    /**
     * @param ResponseInterface $response
     *
     * @return self
     */
    public static function fromResponse(ResponseInterface $response)
    {
        $responseBody = (string) $response->getBody();
        if (empty($responseBody)) {
            return new self($response->getStatusCode(), $response->getReasonPhrase());
        }

        $decodedBody = json_decode($responseBody, true);
        if (!isset($decodedBody['error'])) {
            return new self($response->getStatusCode(), $response->getReasonPhrase());
        }

        $error = $decodedBody['error'];

        return new self($response->getStatusCode(), $error['message'], $error['type'], $error);
    }

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $error;

    /**
     * @param string $statusCode
     * @param string $message
     * @param string $type
     * @param array $error
     */
    public function __construct($statusCode, $message, $type = '', array $error = array())
    {
        parent::__construct($message, $statusCode);

        $this->type = $type;
        $this->error = $error;
    }

    public function getHttpStatusCode()
    {
        return $this->getCode();
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getError()
    {
        return $this->error;
    }
}
