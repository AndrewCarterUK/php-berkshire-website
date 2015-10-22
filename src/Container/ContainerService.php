<?php

namespace PHPBerks\Container;

/**
 * A value object representing service dependencies in class constructor
 * arguments.
 */
class ContainerService
{
    /**
     * @var string
     */
    private $serviceName;

    /**
     * Constructor for the container argument.
     *
     * @param string $serviceName The service name.
     */
    public function __construct($serviceName)
    {
        $this->serviceName = $serviceName;
    }

    /**
     * Retrieve the service name.
     * 
     * @return string The service name.
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }
}
