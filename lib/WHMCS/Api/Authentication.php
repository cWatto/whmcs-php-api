<?php
namespace WHMCS\Api;

class Authentication extends AbstractApi {

    /**
     * @param int $client_id
     * @param $opts
     * @return mixed|string
     * @throws \Http\Client\Exception
     */
    public function createSsoToken(int $client_id, $opts = []) {
        return $this->send('CreateSsoToken', array_merge($opts, [
            'client_id' => $client_id
        ]));
    }


}