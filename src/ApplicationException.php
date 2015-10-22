<?php

namespace PHPBerks;

use Psr\Http\Message\ResponseInterface;

class ApplicationException extends \Exception
{
    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * Create an application exception.
     * 
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;

        parent::__construct($response->getReasonPhrase(), $response->getStatusCode());
    }

    /**
     * Get the response.
     * 
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }
}