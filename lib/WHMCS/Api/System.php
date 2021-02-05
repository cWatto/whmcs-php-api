<?php
namespace WHMCS\Api;

class System extends AbstractApi {

    /**
     * @return mixed|string
     * @throws \Http\Client\Exception
     */
    public function details() {
        return $this->send('WhmcsDetails' );
    }

    /**
     * @return mixed|string
     * @throws \Http\Client\Exception
     */
    public function announcements() {
        return $this->send('GetAnnouncements');
    }

}