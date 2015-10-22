<?php

namespace PHPBerks\Content;

use League\CommonMark\CommonMarkConverter;
use Symfony\Component\Yaml\Yaml;

class ContentLoader
{
    /**
     * @var string
     */
    private $directory;

    /**
     * Constructor.
     * 
     * @param string $directory The directory containing the content.
     */
    public function __construct($directory)
    {
        $this->directory = rtrim($directory, '/').'/';
    }

    /**
     * Get content from a request path.
     * 
     * @param string $requestPath
     * 
     * @throws \InvalidArgumentException When content does not exist.
     */
    public function getContent($requestPath)
    {
        // Safety first kids
        $requestPath = str_replace('..', '', $requestPath);

        $contentPath = $this->directory.ltrim($requestPath,'/').'.md';

        return $this->loadContent($contentPath);
    }

    /**
     * Load content from a markdown file.
     * 
     * @param string $contentPath
     * 
     * @throws \InvalidArgumentException When content does not exist.
     */
    private function loadContent($contentPath)
    {
        if (!file_exists($contentPath)) {
            throw new \InvalidArgumentException('Content does not exist');
        }

        $contents = file_get_contents($contentPath);

        $result = preg_match_all('/^---\n(.+)\n---(.+)$/s', $contents, $matches);

        if ($result === 1) {
            $metadata = $this->getMetadata($matches[1][0]);
            $html     = $this->getHtml($matches[2][0]);
        } else {
            $metadata = [];
            $html     = $this->getHtml($contents);
        }

        return new Content($metadata, $html);
    }

    /**
     * Convert YAML to an array.
     * 
     * @param string $yaml
     * 
     * @param return array
     */
    private function getMetadata($yaml)
    {
        return Yaml::parse($yaml);
    }

    /**
     * Convert markdown to html.
     * 
     * @param string $markdown Markdown input.
     * 
     * @return string $html HTML output.
     */
    private function getHtml($markdown)
    {
        return (new CommonMarkConverter())->convertToHtml($markdown);
    }
}
