<?php
namespace WHMCS\Api;

use WHMCS\Response;

class Client extends AbstractApi {

    /**
     * Get client addons
     * @param int $client_id
     * @param $opts
     * @return Response|string
     * @throws \Http\Client\Exception
     */
    public function products(int $client_id, array $opts = []) {
        return $this->send('GetClientsProducts', array_merge($opts, [
            'clientid' => $client_id
        ]));
    }

    /**
     * @param int $client_id
     * @return Response|string
     * @throws \Http\Client\Exception
     */
    public function details(int $client_id) {
        return $this->send('GetClientsDetails', [
            'clientid' => $client_id,
            'stats' => true
        ]);
    }

    /**
     * @param string $search
     * @param array $opts
     * @return Response|string
     * @throws \Http\Client\Exception
     */
    public function get(string $search, $opts = []){
        return $this->send('GetClients', array_merge($opts, [
            'search' => $search
        ]));
    }



}