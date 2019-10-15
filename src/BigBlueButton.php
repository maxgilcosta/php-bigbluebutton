<?php

namespace sanduhrs;

use GuzzleHttp\Psr7\Uri;
use sanduhrs\BigBlueButton\Server;
use sanduhrs\BigBlueButton\Client;

/**
 * Class BigBlueButton
 *
 * @package sanduhrs
 */
class BigBlueButton
{

    /**
     * The BigBlueButton library version.
     *
     * @var string
     *   A version string.
     */
    const VERSION = '0.9.0';

    /**
     * The BigBlueButton API version.
     *
     * @var string
     *   A version string.
     */
    const API_VERSION = '2.2';

    /**
     * The BigBlueButton server object.
     *
     * @var \sanduhrs\BigBlueButton\Server
     */
    public $server;

    /**
     * The BigBlueButton client object.
     *
     * @var \sanduhrs\BigBlueButton\Client
     */
    public $client;


    /**
     * BigBlueButton constructor.
     *
     * @param string $uri
     * @param string $secret
     * @param string $endpoint
     */
    public function __construct(
        $uri = null,
        $secret = null,
        $endpoint = null
    ) {
        if (!empty($uri) && !empty($secret) && !empty($endpoint)) {
            $uri = new Uri($uri);
            $this->client = new Client($uri, $secret, $endpoint);
            $this->server = new Server($this->client);
        }
    }

    /**
     * Get the version.
     *
     * @return string
     */
    public function getVersion()
    {
        return self::VERSION;
    }

    /**
     * Get the api version.
     *
     * @return string
     */
    public function getApiVersion()
    {
        return self::API_VERSION;
    }

    /**
     * Get the server.
     *
     * @return \sanduhrs\BigBlueButton\Server
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * Set the server.
     *
     * @param \sanduhrs\BigBlueButton\Server $server
     * @return $this
     */
    public function setServer(Server $server)
    {
        $this->server = $server;
        return $this;
    }

    /**
     * Get the client.
     *
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set the client.
     *
     * @param \sanduhrs\BigBlueButton\Client $client
     * @return $this
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
        return $this;
    }
}
