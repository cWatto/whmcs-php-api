<?php
namespace WHMCS\Api;

use WHMCS\Response;

class Invoice extends AbstractApi {

    /**
     * Gets all invoices by a given user id
     * @param int $invoice_id
     * @return Response|string
     * @throws \Http\Client\Exception
     */
    public function get(int $invoice_id) {
        return $this->send('GetInvoice', [
            'invoiceid' => $invoice_id
        ]);
    }

    /**
     * @param array $opts
     * @return Response|string
     * @throws \Http\Client\Exception
     */
    public function all(array $opts = []){
        return $this->send('GetInvoices', $opts);
    }

    /**
     * @param int $user_id
     * @param array $opts
     * @return Response|string
     * @throws \Http\Client\Exception
     */
    public function byUserId(int $user_id, array $opts = []) {
        return $this->send('GetInvoices', array_merge($opts, [
            'userid' => $user_id
        ]));
    }



}