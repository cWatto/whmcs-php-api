<?php
namespace WHMCS\Api;

class Invoice extends AbstractApi {

    /**
     * Gets all invoices by a given user id
     * @param int $invoice_id
     * @return mixed|string
     * @throws \Http\Client\Exception
     */
    public function get(int $invoice_id, array $opts = []) {
        return $this->send('GetInvoice', array_merge($opts, [
            'invoiceid' => $invoice_id
        ]));
    }

    /**
     * @param array $opts
     * @return mixed|string
     * @throws \Http\Client\Exception
     */
    public function all(array $opts = []){
        return $this->send('GetInvoices', $opts);
    }

    /**
     * @param int $user_id
     * @param array $opts
     * @return mixed|string
     * @throws \Http\Client\Exception
     */
    public function byUserId(int $user_id, array $opts = []) {
        return $this->send('GetInvoices', array_merge($opts, [
            'userid' => $user_id
        ]));
    }



}