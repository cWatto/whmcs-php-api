<?php

namespace WHMCS\Tests;

use WHMCS\Client;
use \Psr\Http\Client\ClientInterface;

class ClientTest extends \PHPUnit\Framework\TestCase {

    public function testShouldNotHaveToPassHttpClientToConstructor(){
        $client = new Client('https://test.com', '123', '456');
        $this->assertInstanceOf(ClientInterface::class, $client->getHttpClient());
    }

    public function testShouldPassHttpClientInterfaceToConstructor(){
        $httpClientMock = $this->getMockBuilder(ClientInterface::class)
            ->getMock();

        $client = Client::createWithHttpClient('https://test.com', '123', '456', $httpClientMock);
        $this->assertInstanceOf(ClientInterface::class, $client->getHttpClient());
    }

}