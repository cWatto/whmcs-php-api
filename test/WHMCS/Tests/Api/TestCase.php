<?php

namespace WHMCS\Tests\Api;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use WHMCS\Client;

abstract class TestCase extends \PHPUnit\Framework\TestCase {

    protected function getApiMock()
    {
        $httpClient = $this->getMockBuilder(ClientInterface::class)
            ->setMethods(['sendRequest'])
            ->getMock();
        $httpClient
            ->expects($this->any())
            ->method('sendRequest');

        $client = Client::createWithHttpClient('https://test.com', '', '', $httpClient);

        return $this->getMockBuilder($this->getApiClass())
            ->setMethods(['get', 'post', 'postRaw', 'patch', 'delete', 'put', 'head'])
            ->setConstructorArgs([$client])
            ->getMock();
    }

    /**
     * @return string
     */
    abstract protected function getApiClass();

    /**
     * @param $expectedArray
     *
     * @return Response
     */
    public function getPSR7Response($expectedArray)
    {
        return new Response(
            200,
            ['Content-Type' => 'application/json'],
            \GuzzleHttp\Psr7\stream_for(json_encode($expectedArray))
        );
    }

}