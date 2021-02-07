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
     * @return Response|string
     * @throws \Http\Client\Exception
     */
    public function announcements() {
        return $this->send('GetAnnouncements');
    }

}