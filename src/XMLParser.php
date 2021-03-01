<?php

namespace App;

use http\Exception\InvalidArgumentException;

/**
 * Class Parser
 * Load and parse incoming data.
 */
class XMLParser
{
    /** @var array Parsed incoming data */
    protected $metadata;

    /** @var \SimpleXMLElement Parsed xml content */
    protected $xmlContent;

    /**
     * Open provided file.
     *
     * @param $path_to_file
     */
    public function load($path_to_file): self
    {
        $path = realpath($path_to_file);
        if (! $path) {
            throw new \InvalidArgumentException("File {$path_to_file} not found.");
        }

        if (! $this->xmlContent = simplexml_load_file($path)) {
            throw new \InvalidArgumentException("File {$path_to_file} not correct xml file.");
        }

        return $this;
    }

    /**
     * Parse and return metadata.
     *
     * @return array
     */
    public function getMetadata(): array
    {
        $this->parseMetadata();

        return $this->metadata;
    }

    /**
     * Parse XML metadata from provided file.
     */
    protected function parseMetadata()
    {
        // todo: ns from outside?
        foreach($this->xmlContent->{'silence'} as $object) {
            $this->metadata[] = [
                'from' => (string) $object['from'],
                'until' => (string) $object['until'],
            ];
        }
    }
}