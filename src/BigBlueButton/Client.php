<?php

namespace sanduhrs\BigBlueButton;

use GuzzleHttp\Client as HTTPClient;
use GuzzleHttp\Psr7\Uri;
use sanduhrs\BigBlueButton\Exception\BigBlueButtonException;

/**
 * Class Client
 *
 * @package sanduhrs\BigBlueButton
 */
class Client
{
    /**
     * The server URI.
     *
     * @var \GuzzleHttp\Psr7\Uri
     */
    protected $uri;

    /**
     * The server secret.
     *
     * @var string
     */
    protected $secret;

    /**
     * The api endpoint.
     *
     * @var string
     */
    protected $endpoint;

    /**
     * The HTTP client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Client constructor.
     *
     * @param string $uri
     * @param string $secret
     * @param string $endpoint
     */
    public function __construct(
        $uri,
        $secret,
        $endpoint
    ) {
        $this->uri = new Uri($uri);
        $this->secret = $secret;
        $this->endpoint = $endpoint;
        $this->client = new HTTPClient();
    }

    /**
     * Get URI.
     *
     * @return \GuzzleHttp\Psr7\Uri
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Set URI.
     *
     * @param \GuzzleHttp\Psr7\Uri $uri
     *
     * @return \GuzzleHttp\Psr7\Uri
     */
    public function setUri(Uri $uri)
    {
        return $this->uri = $uri;
    }

    /**
     * Get Secret.
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * Set Secret.
     *
     * @param string $secret
     *
     * @return string
     */
    public function setSecret($secret)
    {
        return $this->secret = $secret;
    }

    /**
     * Get Endpoint.
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * Set Endpoint.
     *
     * @param string $endpoint
     *
     * @return string
     */
    public function setEndpoint($endpoint)
    {
        return $this->endpoint = $endpoint;
    }

    /**
     * Generate checksum.
     *
     * @param string $call
     *   the API call identifier.
     * @param array $options
     *   The API call options.
     *
     * @return string
     *   The generated checksum.
     */
    private function checksum($call, $options)
    {
        // Generate query string.
        $query_string = $this->generateQueryString($options);
        // Generate the checksum.
        $checksum = sha1($call . $query_string . $this->secret);
        return $checksum;
    }

    /**
     * Convert raw response XML to response Object.
     *
     * @param string $raw_response
     *
     * @return object
     */
    private function xml2object($raw_response)
    {
        $xml = simplexml_load_string($raw_response);
        $json = json_encode($xml);
        $array = json_decode($json);

        // Fix data model.
        if (isset($array->attendees)) {
            $array->attendees = (array) $array->attendees;
            if (!isset($array->attendees[0])
                || $array->attendees[0] === "\n") {
                $array->attendees = [];
            }
        }

        // Fix data model.
        if (isset($array->metadata)) {
            $array->metadata = (array) $array->metadata;
            if (!isset($array->metadata[0])
                || $array->metadata[0] === "\n") {
                $array->metadata = [];
            }
        }

        return $array;
    }

    /**
     * Clean response object from unwanted messages.
     *
     * @param object $response
     *
     * @return object
     */
    private function clean($response)
    {
        if (isset($response->returncode)) {
            unset($response->returncode);
        }
        if (isset($response->message)) {
            unset($response->message);
        }
        if (isset($response->messageKey)) {
            unset($response->messageKey);
        }
        foreach ($response as $key => $value) {
            if ($value === 'false') {
                $response->{$key} = false;
            } elseif ($value === 'true') {
                $response->{$key} = true;
            } elseif (is_numeric($value)) {
                $response->{$key} = (int) $value;
            }
        }
        return $response;
    }

    /**
     * Generate call URI.
     *
     * @param string $call
     * @param array $options
     *   The query parameters.
     * @param bool $checksum
     *
     * @return string
     *   A properly formatted call URI.
     */
    public function generateURI($call, $options = [], $checksum = true)
    {
        $options = $this->prepareQueryOptions($options);
        $uri = new Uri(implode('', [
            $this->uri,
            $this->endpoint,
            $call,
            '?',
            $this->generateQueryString($options),
            ($checksum ? '&checksum=' . $this->checksum($call, $options) : ''),
        ]));
        return $uri;
    }

    /**
     * Generate Querystring.
     *
     * @param array $options
     *   The query parameters.
     *
     * @return string
     */
    private function generateQueryString($options = [])
    {
        $query = array();
        // Encode the options.
        foreach ($options as $key => $value) {
            $query[] = $key . '=' . str_replace(['/', ' '], ['%2F', '+'], rawurlencode($value));
        }
        // Generate the querystring.
        return implode('&', $query);
    }

    /**
     * Prepare query options.
     *
     * @param array $options
     *
     * @return array
     */
    private function prepareQueryOptions($options)
    {
        ksort($options, SORT_STRING);
        foreach ($options as $key => $option) {
            if (is_bool($option)) {
                $options[$key] = $option ? 'true' : 'false';
            } elseif (empty($option)) {
                unset($options[$key]);
            }
        }
        return $options;
    }

    /**
     * GET request to API endpoint.
     *
     * @param string $call
     *   The API call string.
     * @param array $options
     *   The API call options.
     *
     * @return string
     *   The xml formatted server response string.
     */
    public function getRaw($call, $options = [])
    {
        $options = $this->prepareQueryOptions($options);
        $options += [
            'checksum' => $this->checksum($call, $options),
        ];
        $response = $this->client->request(
            'GET',
            $this->uri . $this->endpoint . $call,
            ['query' => $options]
        );
        return $response->getBody();
    }

    /**
     * GET request to API endpoint.
     *
     * @param string $call
     *   The API call string.
     * @param array $options
     *   The API call options.
     *
     * @throws \sanduhrs\BigBlueButton\Exception\BigBlueButtonException
     *
     * @return object
     *   The response data as object.
     */
    public function get($call, $options = [])
    {
        $raw_response = $this->getRaw($call, $options);
        $response = $this->xml2object($raw_response);

        if ($response->returncode === 'SUCCESS') {
            $response = $this->clean($response);
        } elseif (isset($response->message)) {
            throw new BigBlueButtonException($response->message);
        } else {
            throw new BigBlueButtonException('An unknown error occurred while communicating with the server.');
        }
        return $response;
    }

    /**
     * POST request to API endpoint.
     *
     * @param string $call
     *   The API call string.
     * @param array $options
     *   The API call options.
     *
     * @return string
     *   The xml formatted server response string.
     */
    public function postRaw($call, $options = [], $content_type = 'application/xml')
    {
        $uri = $this->generateURI($call, [], false);
        $body = isset($options['body']) ? $options['body'] : '';
        unset($options['body']);

        $options = $this->prepareQueryOptions($options);
        $options += [
            'checksum' => $this->checksum($call, $options),
        ];
        $response = $this->client->request(
            'POST',
            $uri,
            [
              'query' => $options,
              'body' => $body,
              'headers' => [
                  'Content-Type' => $content_type,
                  'Content-Length' => mb_strlen($body),
              ],
            ]
        );
        return $response->getBody();
    }

    /**
     * POST request to API endpoint.
     *
     * @param string $call
     *   The API call string.
     * @param array $options
     *   The API call options.
     *
     * @throws \Exception
     *
     * @return string
     *   The json formatted server response string.
     */
    public function post($call, $options = [])
    {
        $raw = $this->postRaw($call, $options);
        $xml = simplexml_load_string($raw);

        list($element) = $xml->xpath('/*/returncode');
        // @todo Throw exception if $element[0] != 'SUCCESS'
        if (isset($element[0])) {
            unset($element[0]);
        }
        // Reformat as json.
        return json_decode(json_encode($xml));
    }
}
