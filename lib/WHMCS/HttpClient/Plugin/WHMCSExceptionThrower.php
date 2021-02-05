<?php


namespace WHMCS\HttpClient\Plugin;


use Exception;
use Http\Client\Common\Plugin;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use WHMCS\Exception\AuthenticationFailedException;
use WHMCS\Exception\InvalidPermissionException;
use WHMCS\HttpClient\ResponseMediator;

class WHMCSExceptionThrower implements Plugin {
    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise {
        return $next($request)->then(function (ResponseInterface $response) use ($request) {

            $result = ResponseMediator::getContent($response);

            if( is_string($result) ){

                if (strpos($result, 'Authentication Failed') !== false) {
                    throw new AuthenticationFailedException();
                }
                if (strpos($result, 'Invalid Permissions') !== false) {
                    $message = json_decode($result->getBody(), true)['message'];
                    throw new InvalidPermissionException($message);
                }

            }



            return $response;
        });
    }
}