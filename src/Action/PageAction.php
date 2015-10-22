<?php

namespace PHPBerks\Action;

use League\Plates\Engine;
use PHPBerks\Content\ContentLoader;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PageAction
{
    /**
     * @var Engine
     */
    private $plates;

    /**
     * @var ContentLoader
     */
    private $contentLoader;

    /**
     * Constructor.
     * 
     * @param Engine $plates
     */
    public function __construct(Engine $plates, ContentLoader $contentLoader)
    {
        $this->plates        = $plates;
        $this->contentLoader = $contentLoader;
    }

    /**
     * Handler.
     * 
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * 
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        $page = $request->getAttribute('page', '/index');

        $content  = $this->contentLoader->getContent($page);
        $template = $content->getMetadataEntry('template');

        $params = array_merge($content->getMetadata(), ['html' => $content->getHtml()]);
        $response->getBody()->write($this->plates->render($template, $params));

        return $response->withHeader('Content-Type', 'text/html');
    }
}
