<?php


namespace WHMCS\Api;


use Psr\Http\Message\StreamInterface;
use WHMCS\Client;
use WHMCS\HttpClient\ResponseMediator;

abstract class AbstractApi {
    /**
     * The client instance
     * @var Client
     */
    private $client;

    /**
     * the offset for the returned data for pagination
     * @var int|null
     */
    private $limitStart;

    /**
     * the offset of the returned data
     * @var int|null
     */
    private $limitNum;

    /**
     * Create a new API instance.
     * @param Client $client
     *
     * @return void
     */
    public function __construct(Client $client){
        $this->client = $client;
    }

    /**
     * Get the client instance
     * @return Client
     */
    protected function getClient(): Client {
        return $this->client;
    }

    /**
     * @return $this
     */
    public function configure(): self {
        return $this;
    }

    /**
     * Sends a POST request
     * @param string $method
     * @param array $parameters
     * @param array $requestHeaders
     *
     * @return mixed|string
     * @throws \Http\Client\Exception
     */
    protected function send(string $method, array $parameters = [], array $requestHeaders = []){
        $parameters = array_merge($parameters, [
            'action' => $method,
            'identifier' => $this->getClient()->getApiIdentifier(),
            'secret' => $this->getClient()->getApiSecret(),
            'responsetype' => 'json'
        ]);

        if( $this->limitStart !== null && !isset($parameters['limitstart'])){
            $parameters['limitstart'] = $this->limitStart;
        }
        if( $this->limitNum != null && !isset($parameters['limitnum'])) {
            $parameters['limitnum'] = $this->limitNum;
        }

        $response = $this->client->getHttpClient()->post('/includes/api.php', $requestHeaders, http_build_query($parameters));

        return ResponseMediator::getContent($response);
    }
}