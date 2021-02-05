<?php

namespace WHMCS\HttpClient;

/*
 * @author Connor Watson <cwatson.co>
 * @thanks KnpLabs <https://github.com/KnpLabs/php-github-api>
 */

use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\HttpMethodsClientInterface;
use Http\Client\Common\Plugin;
use Http\Client\Common\Plugin\Cache\Generator\HeaderCacheKeyGenerator;
use Http\Client\Common\Plugin\CachePlugin;
use Http\Client\Common\PluginClientFactory;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class Builder {

    /**
     * The object that sends our HTTP messages.
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * A HTTP client with all our plugins enabled
     * @var HttpMethodsClientInterface
     */
    private $pluginClient;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @var StreamFactoryInterface
     */
    private $streamFactory;

    /**
     * True if a new plugin client should be created at next request
     * @var bool
     */
    private $httpClientModified = true;

    /**
     * @var Plugin[]
     */
    private $plugins = [];

    /**
     * This plugin is special treated because it has to be the very last plugin.
     * @var Plugin\CachePlugin|null
     */
    private $cachePlugin;

    /**
     * HTTP headers
     * @var array
     */
    private $headers = [];

    /**
     * @param ClientInterface|null $httpClient
     * @param RequestFactoryInterface|null $requestFactory
     * @param StreamFactoryInterface|null $streamFactory
     */
    public function __construct(ClientInterface $httpClient = null,
                                RequestFactoryInterface $requestFactory = null,
                                StreamFactoryInterface $streamFactory = null){

        $this->httpClient = $httpClient ?? Psr18ClientDiscovery::find();
        $this->requestFactory = $requestFactory ?? Psr17FactoryDiscovery::findRequestFactory();
        $this->streamFactory = $streamFactory ?? Psr17FactoryDiscovery::findStreamFactory();
    }

    /**
     * If client modified, adds the cache plugin to the end and recreates the client
     * @return HttpMethodsClientInterface
     */
    public function getHttpClient(): HttpMethodsClientInterface {
        if($this->httpClientModified){
            $this->httpClientModified = false;

            $plugins = $this->plugins;

            if( $this->cachePlugin ){
                $plugins[] = $this->cachePlugin;
            }

            $this->pluginClient = new HttpMethodsClient((new PluginClientFactory())->createClient($this->httpClient, $plugins),
                $this->requestFactory, $this->streamFactory);

        }

        return $this->pluginClient;
    }

    /**
     * Adds a new plugin to the end of the plugin chain
     * @param Plugin $plugin
     * @return void
     */
    public function addPlugin(Plugin $plugin) {
        $this->plugins[] = $plugin;
        $this->httpClientModified = true;
    }

    /**
     * Remove a plugin by it's fully qualified class name
     * @param string $fqcn
     * @return void
     */
    public function removePlugin(string $fqcn): void {
        foreach($this->plugins as $idx => $plugin) {
            if( $plugin instanceof $fqcn ){
                unset($this->plugins[idx]);
                $this->httpClientModified = true;
            }
        }
    }

    /**
     * Clears current headers
     * @return void
     */
    public function clearHeaders(): void {
        $this->headers = [];
        $this->removePlugin(Plugin\HeaderAppendPlugin::class);
        $this->addPlugin(new Plugin\HeaderAppendPlugin($this->headers));
    }

    /**
     * @param array $headers
     * @return void
     */
    public function addHeaders(array $headers): void {
        $this->headers = array_merge($this->headers, $headers);
        $this->removePlugin(Plugin\HeaderAppendPlugin::class);
        $this->addPlugin(new Plugin\HeaderAppendPlugin($this->headers));
    }

    /**
     * @param string $header
     * @param string $headerValue
     *
     * @return void
     */
    public function addHeaderValue(string $header, string $headerValue): void {
        if( !isset($this->headers[$header]) ) {
            $this->headers[$header] = $headerValue;
        }else{
            $this->headers[$header] = array_merge((array) $this->headers[$header], [$headerValue]);
        }

        $this->removePlugin(Plugin\HeaderAppendPlugin::class);
        $this->addPlugin(new Plugin\HeaderAppendPlugin($this->headers));
    }

    /**
     * Adds a cache plugin to cache responses locally
     *
     * @param CacheItemPoolInterface $cachePool
     * @param array $config
     *
     * @return void
     */
    public function addCache(CacheItemPoolInterface $cachePool, array &$config = []): void {
        if(!isset($config['cache_key_generator'])) {
            $config['cache_key_generator'] = new HeaderCacheKeyGenerator(['Authorization', 'Cookie', 'Accept', 'Content-type']);
        }
        $this->cachePlugin = CachePlugin::clientCache($cachePool, $this->streamFactory, $config);
        $this->httpClientModified = true;
    }

    /**
     * Removes the cache plugin
     *
     * @return void
     */
    public function removeCache(): void {
        $this->cachePlugin = null;
        $this->httpClientModified = true;
    }

}