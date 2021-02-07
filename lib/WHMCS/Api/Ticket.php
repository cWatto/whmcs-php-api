<?php
namespace WHMCS\Api;

use WHMCS\Response;

class Ticket extends AbstractApi {

    /**
     * Returns all the tickets
     * @param array $opts
     * @return Response|string
     * @throws \Http\Client\Exception
     */
    public function all(array $opts = []) {
        return $this->send('GetTickets', $opts);
    }

    /**
     * Gets all tickets by a given user id
     * @param int $client_id
     * @param array $opts
     * @return Response|string
     * @throws \Http\Client\Exception
     */
    public function byUserId(int $client_id, $opts = []) {
        return $this->send('GetTickets', array_merge([
            'clientid' => $client_id
        ], $opts));
    }

    /**
     * Gets all the support departments of the WHMCS installation
     * @return Response|string
     * @throws \Http\Client\Exception
     */
    public function departments() {
        return $this->send('GetSupportDepartments');
    }

    /**
     * @return Response|string
     * @throws \Http\Client\Exception
     */
    public function statuses() {
        return $this->send('GetSuportStatuses');
    }

    /**
     * Returns a ticket by id
     * @param $ticket_id
     * @param $opts
     * @return Response|string
     * @throws \Http\Client\Exception
     */
    public function get($ticket_id, $opts){
        return $this->send('GetTicket', array_merge([
            'ticketid' => $ticket_id
        ], $opts));
    }

    /**
     * Gets a specific attachment
     * @param int $attachment_id
     * @param string $attachment_type
     * @param int $index
     * @return Response|string
     * @throws \Http\Client\Exception
     */
    public function attachment(int $attachment_id, string $attachment_type, int $index) {
        return $this->send('GetTicketAttachment', [
           'relatedid' => $attachment_id,
            'type' => $attachment_type,
            'index' => $index
        ]);
    }

    /**
     * @param int $client_id
     * @param string $message
     * @param array $opts
     * @return Response|string
     * @throws \Http\Client\Exception
     */
    public function open(int $client_id,string $message, array $opts){
        return $this->send('OpenTicket', array_merge($opts, [
            'clientid' => $client_id
        ]));
    }

    /**
     * @param int $ticket_id
     * @param string $message
     * @param array $opts
     * @return Response|string
     * @throws \Http\Client\Exception
     */
    public function addReply(int $ticket_id, string $message, array $opts){
        return $this->send('AddTicketReply', array_merge($opts, [
            'ticketid' => $ticket_id,
            'message' => $message
        ]));
    }


}