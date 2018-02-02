<?php

namespace App\Providers;

use GuzzleHttp\Client as HttpClient;

class LeadCertificateProvider
{
    public function sendLeadInformationToTFMSCrawler($firstName,$lastName,$emailAddress,$address1,$address2,$city,$state,$zipCode,$phoneNumber,$dob,$certType)
    {
        // Get a new instance of the HttpClient (Guzzle Http)
        $guzzleClient = new HttpClient();

        // POST to the tfmsCrawlerURL.
        $res = $guzzleClient->request("POST","http://tfms.agent.leadreserve.com:3000/headless-process",[
            "json" => [
                "first_name" => $firstName,
                "last_name" => $lastName,
                "email_address" => $emailAddress,
                "address1" => $address1,
                "address2" => $address2,
                "city" => $city,
                "state" => $state,
                "zipcode" => $zipCode,
                "phonenumber" => $phoneNumber,
                "dob" => $dob
            ]
        ]);

        // TODO: Safely return this.
        return json_decode($res->getBody()->getContents(),1)[$certType];
    }
}
