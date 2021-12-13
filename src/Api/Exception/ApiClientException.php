<?php

namespace Fiizy\Api\Exception;

use LogicException;
use Psr\Http\Message\ResponseInterface;

/**
 * Exception thrown for HTTP 4xx client errors.
 */
class ApiClientException extends LogicException implements ApiExceptionInterface
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
        if (!isset($decodedBody['errors'])) {
            return new self($response->getStatusCode(), $response->getReasonPhrase());
        }

        return new self($response->getStatusCode(), 'client exception', $decodedBody['errors']);
    }

    /**
     * @var array
     */
    private $errors;

    /**
     * @param string $statusCode
     * @param string $message
     * @param string $type
     * @param array $errors
     */
    public function __construct($statusCode, $message, array $errors = array())
    {
        parent::__construct($message, $statusCode);

        $this->errors = $errors;
    }

    public function getHttpStatusCode()
    {
        return $this->getCode();
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
