<?php
namespace WHMCS\Api;

use WHMCS\Response;

class Domain extends AbstractApi {

    /**
     * Gets all tickets by a given user id
     * @param int $client_id
     * @param array $opts
     * @return Response|string
     * @throws \Http\Client\Exception
     */
    public function byUserId(int $client_id, array $opts = []) {
        return $this->send('GetClientsDomains', array_merge([
            'clientid' => $client_id
        ], $opts));
    }

    /**
     * Gets a domain by domain id or by domain string
     * @param int|string $domain
     * @param $opts
     * @return Response|string
     * @throws \Http\Client\Exception
     */
    public function get($domain, array $opts = []){
        if( is_int($domain) ){
            $opts['domainid'] = $domain;
        }else{
            $opts['domain'] = $domain;
        }
        return $this->send('GetClientsDomains', $opts);
    }

    /**
     * @param string $domain
     * @return Response|string
     * @throws \Http\Client\Exception
     */
    public function getWhois( string $domain ) {
        return $this->send('DomainWhois', [
            'domain' => $domain
        ]);
    }

    /**
     * @param $domain_id
     * @return Response|string
     * @throws \Http\Client\Exception
     */
    public function getNameservers(int $domain_id){
        return $this->send('DomainGetNameservers', [
            'domainid' => $domain_id
        ]);
    }

    /**
     * @param $domain_id
     * @param $opts
     * @return Response|string
     * @throws \Http\Client\Exception
     */
    public function updateNameservers(int $domain_id, array $opts = []){
        return $this->send('DomainUpdateNameservers', array_merge([
            'domainid' => $domain_id
        ], $opts));
    }

    /**
     * Sends the renew command to the registrar, regperiod will default to the system default if left blank
     * @param $domain_id
     * @param int|null $regperiod
     * @return Response|string
     * @throws \Http\Client\Exception
     */
    public function renew(int $domain_id, int $regperiod = null) {
        $query_params = [
            'domainid' => $domain_id
        ];
        if( $regperiod ){
            $query_params['regperiod'] = $regperiod;
        }
        return $this->send('DomainRenew', $query_params);
    }


}