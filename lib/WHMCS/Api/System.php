<?php
namespace WHMCS\Api;

use WHMCS\Response;

class System extends AbstractApi {

    /**
     * @return Response|string
     * @throws \Http\Client\Exception
     */
    public function details() {
        return $this->send('WhmcsDetails' );
    }

    /**
     * @param array $opts
     * @return Response|string
     * @throws \Http\Client\Exception
     */
    public function announcements($opts = []) {
        return $this->send('GetAnnouncements', $opts);
    }

}