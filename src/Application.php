<?php

namespace PHPBerks;

use FastRoute;
use PHPBerks\Container\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response\SapiEmitter;

class Application
{
    /**
     * @var FastRoute\Dispatcher
     */
    private $dispatcher;

    /**
     * @var Container
     */
    private $container;

    /**
     * Constructor.
     * 
     * @param Container $container The applications DI container.
     */
    public function __construct(Container $container)
    {
        $this->container = $container;

        $this->createDispatcher();
    }

    /**
     * Instantiates the router using the routes in the container.
     */
    private function createDispatcher()
    {
        $this->dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $collector) {
            foreach ($this->container->get('routes') as $route) {
                $collector->addRoute($route['method'], $route['pattern'], $route['service']);
            }
        });
    }

    /**
     * Run the application.
     * 
     * @param ServerRequestInterface $request  Optional request message to use.
     * @param ResponseInterface      $response Optional response message to use.
     */
    public function run(ServerRequestInterface $request = null, ResponseInterface $response = null)
    {
        $request  = $request  ?: ServerRequestFactory::fromGlobals();
        $response = $response ?: new Response;

        try {
            $response = $this->getResponse($request, $response);
        } catch (ApplicationException $exception) {
            $response = $exception->getResponse();
        }

        (new SapiEmitter)->emit($response);
    }

    /**
     * Get the handler service name for a request.
     * 
     * @param ServerRequestInterface $request  Request.
     * @param ResponseInterface      $response Response.
     * 
     * @return ResponseInterface The response.
     * 
     * @throws ApplicationException On request not found or method not allowed.
     */
    public function getResponse(ServerRequestInterface $request, ResponseInterface $response)
    {
        $method = $request->getMethod();
        $path   = $request->getUri()->getPath();

        $routeInfo = $this->dispatcher->dispatch($method, $path);

        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::FOUND: break;

            case FastRoute\Dispatcher::NOT_FOUND:
                throw $this->createNotFoundException();

            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                throw $this->createMethodNotAllowedException();

            default:
                throw new \Exception('Unexpected dispatcher response');
        }

        foreach ($routeInfo[2] as $name => $value) {
            $request = $request->withAttribute($name, $value);
        }

        $service = $this->container->get($routeInfo[1]);

        if (!is_callable($service)) {
            throw new \Exception('Route handler is not callable');
        }

        return $service($request, $response);
    }

    /**
     * Create a not found exception.
     * 
     * @return ApplicationException
     */
    private function createNotFoundException()
    {
        $response = (new Response)->withStatus(404);
        $response->getBody()->write('Not Found');
        return new ApplicationException($response);
    }

    /**
     * Create a method not allowed exception.
     * 
     * @return ApplicationException
     */
    private function createMethodNotAllowedException()
    {
        $response = (new Response)->withStatus(405);
        $response->getBody()->write('Method Not Allowed');
        return new ApplicationException($response);
    }
}
