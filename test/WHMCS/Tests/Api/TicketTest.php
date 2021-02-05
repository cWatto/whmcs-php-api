<?php

namespace WHMCS\Tests\Api;

use http\Client\Response;
use Psr\Http\Message\ResponseInterface;

class TicketTest extends TestCase
{

    public function testShouldGetTickets(){
        $expectedValue = [
            [
                'organization' => [
                    'login' => 'octocat',
                    'id'    => 1,
                ],
                'user'         => [
                    'login' => 'defunkt',
                    'id'    => 3,
                ],
            ],
            [
                'organization' => [
                    'login' => 'invitocat',
                    'id'    => 2,
                ],
                'user'         => [
                    'login' => 'defunkt',
                    'id'    => 3,
                ],
            ],
        ];
        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->will($this->returnValue($this->getPSR7Response($expectedValue)));

        $this->assertEquals($expectedValue, $api->all());
    }
    /**
     * @return string
     */
    protected function getApiClass()
    {
        return \WHMCS\Api\Ticket::class;
    }

}