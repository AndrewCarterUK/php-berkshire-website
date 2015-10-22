<?php

namespace PHPBerks\Content;

class Content
{
    /**
     * @var array
     */
    private $metadata;

    /**
     * @var string
     */
    private $html;

    /**
     * Constructor.
     * 
     * @param array $metadata The content metadata.
     * @param type  $html     The content HTML.
     */
    public function __construct(array $metadata, $html)
    {
        $this->metadata = $metadata;
        $this->html     = $html;
    }

    /**
     * Get all of the metadata.
     * 
     * @return array The meta data.
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Extract a value from the metadata.
     * 
     * @param string $key The metadata key.
     * 
     * @return mixed The metadata entry.
     * 
     * @throws \InvalidArgumentException When the key is not present in the metadata.
     */
    public function getMetadataEntry($key)
    {
        if (!isset($this->metadata[$key])) {
            throw new \InvalidArgumentException('Key does not exist in metadata: '.$key);
        }

        return $this->metadata[$key];
    }

    /**
     * Get the HTML.
     * 
     * @return string The HTML.
     */
    public function getHtml()
    {
        return $this->html;
    }
}