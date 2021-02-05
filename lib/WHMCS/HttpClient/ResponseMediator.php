<?php

namespace WHMCS\HttpClient;

use Psr\Http\Message\ResponseInterface;

class ResponseMediator {

    public static function getContent(ResponseInterface $response) {
        $body = $response->getBody()->__toString();
        if (strpos($response->getHeaderLine('Content-Type'), 'application/json') === 0) {
            $content = json_decode($body, true);
            if (JSON_ERROR_NONE === json_last_error()) {
                return $content;
            }
        }

        return $body;
    }
}