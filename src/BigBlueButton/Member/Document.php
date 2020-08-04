<?php

namespace sanduhrs\BigBlueButton\Member;

use sanduhrs\BigBlueButton\Client;
use GuzzleHttp\Psr7\Uri;

/**
 * Class Document.
 *
 * @package sanduhrs\BigBlueButton
 */
class Document
{

    const XML_VERSION = '1.0';

    const XML_ENCODING = 'UTF-8';

    /**
     * The URI.
     *
     * @var \GuzzleHttp\Psr7\Uri
     */
    protected $uri;

    /**
     * The name.
     *
     * @var string
     */
    protected $name;

    /**
     * Embed the document.
     *
     * @var bool
     */
    protected $embed;

    /**
     * The base64 representation.
     *
     * @var string
     */
    protected $base64;

    /**
     * The BigBlueButton client.
     *
     * @var \sanduhrs\BigBlueButton\Client
     */
    protected $client;

    /**
     * Document constructor.
     *
     * @param $url
     * @param string $name
     * @param bool $embed
     * @param string $base64
     */
    public function __construct($url = '', $name = '', $embed = false, $base64 = '')
    {
        $this->setUri(new Uri($url));
        $this->setName($name);
        $this->setEmbed($embed);
        $this->setBase64($base64);
    }

    /**
     * Get the URI.
     *
     * @return \GuzzleHttp\Psr7\Uri
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Set the URI.
     *
     * @param \GuzzleHttp\Psr7\Uri $uri
     * @return Document
     */
    public function setUri(Uri $uri)
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name.
     *
     * @param string $name
     * @return Document
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Is the document embedded?
     *
     * @return boolean
     */
    public function isEmbedded()
    {
        return $this->embed;
    }

    /**
     * Set the embed indicator.
     *
     * @param bool $embed
     * @return Document
     */
    public function setEmbed($embed)
    {
        $this->embed = $embed;
        return $this;
    }

    /**
     * Get the base64 representation.
     *
     * @return string
     */
    public function getBase64()
    {
        return $this->base64;
    }

    /**
     * Set the base64 representation.
     *
     * @param string $base64
     * @return Document
     */
    public function setBase64($base64)
    {
        $this->base64 = $base64;
        return $this;
    }

    /**
     * Get XML representation of the document for upload.
     *
     * @return \DOMElement
     */
    public function getXmlString()
    {
        $xml = new \DOMDocument(self::XML_VERSION, self::XML_ENCODING);
        if ($this->isEmbedded()) {
            if ($this->getBase64() === '') {
                $this->setBase64(base64_encode(file_get_contents($this->getUri())));
            }
            $document = $xml->createElement("document", $this->getBase64());
            $document->setAttribute('name', $this->getName());
            $xml->appendChild($document);
        } else {
            $document = $xml->createElement("document", $this->getBase64());
            $document->setAttribute('url', $this->getUri());
            $document->setAttribute('filename', $this->getName());
            $xml->appendChild($document);
        }
        return $xml->saveXML($xml->documentElement);
    }

    /**
     * Set the client.
     *
     * @var \sanduhrs\BigBlueButton\Client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }
}
