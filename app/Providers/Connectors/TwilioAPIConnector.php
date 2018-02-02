<?php

namespace App\Providers\Connectors;

use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class TwilioAPIConnector {

    /**
     * @var $sid
     * @var $token
     * @var $client
     */
    public $sid, $token, $client;

    /**
     * Send message to Twilio service.
     * @param $message
     */
    public function sendMessage($message) {
        $this->initConnector();
        foreach($this->outgoingNumberFactory() as $number) {
            $this->client->messages->create(
                $number,
                [
                    'from' => '+19492025163',
                    'body' => $message
                ]
            );
        }
    }

    /**
     * Initiate the current connector.
     */
    private function initConnector() {
        $this->sid = "ACf9c6f06c02da4a709f156ec489c445ce";
        $this->token = "fda028e2dd1b388300feb621eecceb72";
        $this->client = (new Client($this->sid,$this->token));
    }

    private function outgoingNumberFactory() {
        return [
            '+19499454814',
            '+13109908261'
        ];
    }
}