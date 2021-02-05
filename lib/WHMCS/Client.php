<?php

namespace WHMCS;

use BadMethodCallException;
use Http\Client\Common\HttpMethodsClientInterface;
use Http\Client\Common\Plugin\AddHostPlugin;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use Http\Client\Common\Plugin\RedirectPlugin;
use Http\Discovery\Psr17FactoryDiscovery;
use http\Exception\InvalidArgumentException;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Client\ClientInterface;
use WHMCS\Api\AbstractApi;
use WHMCS\Api\Authentication;
use WHMCS\Api\Domain;
use WHMCS\Api\Invoice;
use WHMCS\Api\System;
use WHMCS\Api\Ticket;
use WHMCS\HttpClient\Builder;
use WHMCS\HttpClient\Plugin\WHMCSExceptionThrower;

/**
 * WHMCS API Client :)
 *
 * @method Ticket ticket()
 * @method Api\Client client()
 * @method System system()
 * @method Invoice invoice()
 * @method Authentication auth()
 * @method Domain domain()
 *
 */

class Client {

    private $API_URL = '';
    /**
     * @var string
     */
    private $API_IDENTIFIER = '';

    /**
     * @var string
     */
    private $API_SECRET = '';

    /**
     * @var Builder
     */
    private $httpClientBuilder;

    /**
     * Instantiate a new WHMCS API Client
     * @param Builder|null $httpClientBuilder
     * @param null $apiUrl
     * @param null $apiIdentifier
     * @param null $apiSecret
     *
     * @throws \Exception
     */
    public function __construct($apiUrl, $apiIdentifier, $apiSecret, Builder $httpClientBuilder = null) {
        $this->httpClientBuilder = $builder = $httpClientBuilder ?? new Builder();
        $builder->addPlugin(new WHMCSExceptionThrower());
        $builder->addPlugin(new AddHostPlugin(Psr17FactoryDiscovery::findUriFactory()->createUri($apiUrl)));
        $builder->addPlugin(new HeaderDefaultsPlugin([
            'User-Agent' => 'whmcs-php-api',
            'Accept' => 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded'
        ]));

        $this->API_IDENTIFIER = $apiIdentifier;
        $this->API_SECRET = $apiSecret;
        //$builder->addPlugin(new RedirectPlugin());
    }

    /**
     * Create a WHMCS\Client using a HTTP client.
     *
     * @param $apiUrl
     * @param $apiIdentifier
     * @param $apiSecret
     * @param ClientInterface $httpClient
     *
     * @return Client
     * @throws \Exception
     */
    public static function createWithHttpClient($apiUrl, $apiIdentifier, $apiSecret, ClientInterface $httpClient): self {
        $builder = new Builder($httpClient);
        return new self($apiUrl, $apiIdentifier, $apiSecret, $builder);
    }

    /**
     * @param string $name
     * @return AbstractApi
     */
    public function api($name): AbstractApi {
        switch($name) {
            case 'tickets':
                $api = new Api\Ticket($this);
                break;
            case 'domain':
            case 'domains':
                $api = new Api\Domain($this);
                break;
            case 'client':
                $api = new Api\Client($this);
                break;
            case 'auth':
                $api = new Api\Authentication($this);
                break;
            case 'invoice':
                $api = new Api\Invoice($this);
                break;
            case 'system':
                $api = new Api\System($this);
                break;
            default:
                throw new InvalidArgumentException(sprintf('Undefined api instance called: "%s"', $name));
        }
        return $api;
    }

    /**
     * Add a cache plugin to cache responses locally
     * @param CacheItemPoolInterface $cachePool
     * @param array $config
     */
    public function addCache(CacheItemPoolInterface $cachePool, array $config = []): void {
        $this->getHttpClientBuilder()->addCache($cachePool, $config);
    }

    /**
     * Remove the cache plugin
     * @return void
     */
    public function removeCache(): void {
        $this->getHttpClientBuilder()->removeCache();
    }

    /**
     * @param $name
     * @param $args
     *
     * @return AbstractApi
     */
    public function __call($name, $args): AbstractApi {
        try{
            return $this->api($name);
        }catch(InvalidArgumentException $e){
            throw new BadMethodCallException(sprintf('Undefined method called: "%s"', $name));
        }
    }

    /**
     * @return HttpMethodsClientInterface
     */
    public function getHttpClient(): HttpMethodsClientInterface
    {
        return $this->getHttpClientBuilder()->getHttpClient();
    }

    /**
     * @return Builder
     */
    protected function getHttpClientBuilder(): Builder
    {
        return $this->httpClientBuilder;
    }

    /**
     * @return string
     */
    public function getApiIdentifier(): string {
        return $this->API_IDENTIFIER;
    }

    /**
     * @return string
     */
    public function getApiSecret(): string {
        return $this->API_SECRET;
    }
}