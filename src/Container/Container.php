<?php

namespace PHPBerks\Container;

/**
 * A very simple dependency injection container.
 */
class Container
{
    /**
     * @var array
     */
    private $services;

    /**
     * @var array
     */
    private $classes;

    /**
     * Constructor for the application container. This container currently
     * supports two keys, each containing their own array of services where
     * the key is the service name.
     * 
     * The first supported key is 'services'. The values in this array are
     * ready to use services.
     * 
     * The second supported key is 'classes'. Each element in this array must
     * be an array with a 'class' key and an optional 'arguments' key. Where
     * present the arguments will be passed to the class constructor. If an
     * argument is an instance of ContainerService the argument will be replaced
     * with the corresponding service from the container before the class is
     * instantiated.
     * 
     * @param array $configuration The service configuration.
     */
    public function __construct(array $configuration)
    {
        foreach (['services', 'classes'] as $key) {
            $this->$key = isset($configuration[$key]) ? $configuration[$key] : [];
        }
    }

    /**
     * Retrieve a service from the container.
     * 
     * @param string $name The service name.
     * 
     * @return mixed The service.
     */
    public function get($name)
    {
        // If we have already got it
        if (isset($this->services[$name])) {
            return $this->services[$name];
        }

        // Otherwise, try to create it
        $this->services[$name] = $this->createService($name);

        return $this->services[$name];
    }

    /**
     * Attempt to create a service.
     * 
     * @param string $name Service name.
     * 
     * @return mixed The created service.
     * 
     * @throws \Exception On failure.
     */
    private function createService($name)
    {
        if (isset($this->classes[$name])) {
            return $this->createFromClass($name, $this->classes[$name]);
        }

        throw new \Exception('Could not create service: '.$name);
    }

    /**
     * Create a service using a class entry.
     * 
     * @param string $name       The service name.
     * @param array  $classEntry The class entry.
     * 
     * @return mixed The created service.
     * 
     * @throws \Exception On invalid class entry.
     */
    private function createFromClass($name, $classEntry)
    {
        if (!is_array($classEntry) || !isset($classEntry['class'])) {
            throw new \Exception($name.' service class entry must be an array containing a \'class\' key');
        }

        $arguments = [];

        if (isset($classEntry['arguments'])) {
            foreach ($classEntry['arguments'] as $argument) {
                if ($argument instanceof ContainerService) {
                    $argumentName = $argument->getServiceName();

                    if ($argumentName === $name) {
                        throw new \Exception($name.' service contains a circular reference');
                    }

                    $arguments[] = $this->get($argumentName);
                } else {
                    $arguments[] = $argument;
                }
            }
        }

        $reflector = new \ReflectionClass($classEntry['class']);
        return $reflector->newInstanceArgs($arguments);
    }
}
