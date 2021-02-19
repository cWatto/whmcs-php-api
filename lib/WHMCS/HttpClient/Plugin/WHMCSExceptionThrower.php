<?php


namespace WHMCS\HttpClient\Plugin;


use Http\Client\Common\Plugin;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use WHMCS\Exception\AuthenticationFailedException;
use WHMCS\Exception\InvalidPermissionException;
use WHMCS\Exception\IPNotPermittedException;
use WHMCS\HttpClient\ResponseMediator;

/**
 * Class WHMCSExceptionThrower
 * @package WHMCS\HttpClient\Plugin
 *
 * Catches any errors that WHMCS API may throw and how we should handle them.
 * Identifier/Secret missing -  "Authentication Failed"
 * If identifier is wrong -     "Invalid or missing credentials"
 * If secret is wrong -         "Authentication Failed"
 * If action not permitted      "Invalid Permissions: API action \"getclientsdomainssda\" is not allowed"
 * Invalid IP (not permitted)   "Invalid IP 127.0.0.1"
 */
class WHMCSExceptionThrower implements Plugin {
    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise {
        return $next($request)->then(function (ResponseInterface $response) use ($request) {
            $result = ResponseMediator::getContent($response);

            if( is_string($result) ){
                // Some WHMCS errors return as just strings for annoying reasons, catch them here if needed
            }

            if(!$result->success){
                if(strpos($result->errorMessage, 'missing credentials') !== false){
                    throw new AuthenticationFailedException($result->errorMessage);

                }else if(strpos($result->errorMessage, 'Invalid Permissions') !== false){
                    throw new InvalidPermissionException($result->errorMessage);

                }else if(strpos($result->errorMessage, 'Authentication Failed') !== false) {
                    throw new AuthenticationFailedException($result->errorMessage);

                }else if(strpos($result->errorMessage, 'Invalid IP') !== false){
                    throw new IPNotPermittedException($result->errorMessage);
                }
            }

            return $response;
        });
    }
}