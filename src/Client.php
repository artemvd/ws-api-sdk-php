<?php
namespace Dokobit;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Dokobit\Exception;
use Dokobit\Exception\InvalidApiKey;
use Dokobit\Http\ClientInterface;
use Dokobit\Http\GuzzleClientAdapter;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Dokobit.com WS API client
 */
class Client
{
    /** @var boolean use sandbox */
    private $sandbox = false;

    /** @var string API access key, given by Dokobit.com developer support */
    private $apiKey = null;

    /** @var string production API url */
    private $url = 'https://ws.dokobit.com';

    /** @var string sandbox mode API url. Used if $sandbox is true */
    private $sandboxUrl = 'https://developers.dokobit.com';

    /** @var ClientInterface HTTP client */
    private $client;

    /** @var ResponseMapperInterface response to result object mapper */
    private $responseMapper;

    /** @var ValidatorInterface */
    private $validator;

    /**
     * Public factory method to create instance of Client.
     *
     * @param array $options Available properties: [
     *     'apiKey' => 'xxxxxx',
     *     'sandbox' => true,
     *     'url' => 'https://ws.dokobit.com',
     *     'sandboxUrl' => 'https://developers.dokobit.com',
     * ]
     * @param LoggerInterface|callable|resource|null $log Logger used to log
     *     messages. Pass a LoggerInterface to use a PSR-3 logger. Pass a
     *     callable to log messages to a function that accepts a string of
     *     data. Pass a resource returned from ``fopen()`` to log to an open
     *     resource. Pass null or leave empty to write log messages using
     *     ``echo()``.
     * @return self
     */
    public static function create(array $options = [], $log = false)
    {
        $stack = HandlerStack::create();
        if ($log !== false) {
            $stack->push(
                Middleware::log($log, new MessageFormatter(MessageFormatter::DEBUG))
            );
        }

        $client = new \GuzzleHttp\Client(['handler' => $stack]);

        return new self(
            new GuzzleClientAdapter($client),
            new ResponseMapper(),
            Validation::createValidator(),
            $options
        );
    }

    /**
     * @param ClientInterface $client
     * @param ResponseMapperInterface $responseMapper
     * @param ValidatorInterface $validator
     * @param array $options
     */
    public function __construct(
        ClientInterface $client,
        ResponseMapperInterface $responseMapper,
        ValidatorInterface $validator,
        array $options = []
    ) {
        $this->validateOptions($options);
        $this->prepareOptions($options);

        $this->client = $client;
        $this->responseMapper = $responseMapper;
        $this->validator = $validator;
    }

    /**
     * Get result by given query object
     * @param QueryInterface $query
     * @return ResultInterface
     */
    public function get(QueryInterface $query)
    {
        $this->validate($query);
        $fields = $query->getFields();
        $token = $this->getToken($query);

        return $this->responseMapper->map(
            $this->request(
                $query->getMethod(),
                $this->getFullMethodUrl($query->getAction(), $token),
                $fields
            ),
            $query->createResult()
        );
    }

    /**
     * Check if sandbox enabled
     * @return boolean
     */
    public function isSandbox()
    {
        return $this->sandbox;
    }

    /**
     * Get API access key
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Get production API url
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get sandbox API url
     * @return string
     */
    public function getSandboxUrl()
    {
        return $this->sandboxUrl;
    }

    /**
     * Get full API method url by given action and token.
     * Checks if sandbox is enabled, then uses $sandboxUrl, otherwise
     * production's $url
     * @param string $action
     * @param string $token
     * @return string
     */
    public function getFullMethodUrl($action, $token = '')
    {
        $url = $this->url;
        if ($this->sandbox) {
            $url = $this->sandboxUrl;
        }

        if ($token) {
            $action .= '/'.$token;
        }

        return $url.'/'.$action.'.json';
    }

    /**
     * Get token from TokenizedQueryInterface query.
     * Otherwise return empty string.
     * @param QueryInterface $query
     * @return string
     */
    public function getToken(QueryInterface $query)
    {
        $token = '';
        if ($query instanceof TokenizedQueryInterface) {
            $token = $query->getToken();
        }

        return $token;
    }

    /**
     * Handle request options and perform HTTP request using HTTP client.
     * @param string $method
     * @param string $url
     * @param array $fields
     * @return array
     */
    private function request($method, $url, array $fields)
    {
        $options = [
            'query' => [
                'access_token' => $this->getApiKey()
            ],
            'form_params' => $fields,
        ];

        return $this->client->sendRequest($method, $url, $options);
    }

    /**
     * Read options from array and set values as object properties
     * @param array $options
     * @return void
     */
    private function prepareOptions(array $options)
    {
        if (isset($options['sandbox'])) {
            $this->sandbox = (bool) $options['sandbox'];
        }
        if (isset($options['apiKey'])) {
            $this->apiKey = (string) $options['apiKey'];
        }
        if (isset($options['url'])) {
            $this->url = (string) $options['url'];
        }
        if (isset($options['sandboxUrl'])) {
            $this->sandboxUrl = (string) $options['sandboxUrl'];
        }
    }

    /**
     * Validate options
     * @param array $options
     * @return void
     * @throws InvalidApiKey if no API key given
     */
    private function validateOptions(array $options)
    {
        if (empty($options['apiKey'])) {
            throw new Exception\InvalidApiKey('Access forbidden. Invalid API key.', 0);
        }
    }

    /**
     * Validate query parameters
     * @param QueryInterface $query
     * @return void
     */
    private function validate(QueryInterface $query)
    {
        $violations = $this->validator->validate(
            $query->getFields(),
            $query->getValidationConstraints()
        );

        if (count($violations) !== 0) {
            throw new Exception\QueryValidator('Query parameters validation failed', $violations);
        }
    }
}
