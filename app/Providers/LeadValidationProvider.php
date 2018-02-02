<?php

namespace App\Providers;

use App\Jobs\TrackPlatformEventAsync;

class LeadValidationProvider
{
    /**
     * Check the current lead against all validations.
     * @param $leadObject
     * @param $campaign
     * @return bool
     */
    public function checkValidations($leadObject, $campaign)
    {
        // A validation array.
        $validations = [
            "fieldExclusion" => [
                "status" => false,
                "message" => ""
            ],
            "datetime" => [
                "status" => false,
                "message" => ""
            ],
            "blacklist" => [
                "status" => false,
                "message" => ""
            ],
            "age" => [
                "status" => false,
                "message" => ""
            ],
            "gender" => [
                "status" => false,
                "message" => ""
            ]
        ];

        // 0-int
        $validationsSuccessful = 0;

        // Go through each validation procedure.
        foreach ($validations as $validation => &$object) {
            $validation .= "Check";
            if (method_exists($this, $validation)) {
                $methodCheck = $this->$validation($leadObject, $campaign);
                $object = [
                    "status" => $methodCheck["status"],
                    "message" => $methodCheck["message"]
                ];
                if ($methodCheck["status"]) {
                    $validationsSuccessful++;
                }
            }
        }

        // Track lead validation.
        dispatch((new TrackPlatformEventAsync("lead.validation", "Validating " . $leadObject->id, [
            "lead_id" => $leadObject->id,
            "successful_validations" => $validationsSuccessful,
            "validations" => $validations
        ]))->onQueue("platform-processing"));

        $validationMessages = [];
        foreach ($validations as $validation => $object) {
            if (!$object["status"]) {
                $validationMessages[] = $object["message"];
            }
        }

        $returnObject = [
            "status" => (
                $validations["fieldExclusion"]["status"] &&
                $validations["blacklist"]["status"] &&
                $validations["datetime"]["status"] &&
                $validations["age"]["status"] &&
                $validations["gender"]["status"]
            ),
            "message" => implode(", ", $validationMessages)
        ];

        // Go ahead and return complete success.
        return $returnObject;
    }

    /**
     * @param $leadObject
     * @param $campaign
     * @return bool
     */
    private function fieldExclusionCheck($leadObject, $campaign)
    {
        // Create an array to store the state of field checks.
        $fieldChecks = [
            "zip" => false,
            "state" => false
        ];

        // Create a response object.
        $returnObject = [
            "status" => false,
            "message" => ""
        ];

        // Ziplist check.
        if ($campaign->hasAttributeOrEmpty("ziplist_exclusion") != "") {
            $fieldChecks["zip"] = !in_array(
                $leadObject->hasPointOrEmpty("zipcode"),
                explode(",", $campaign->hasAttributeOrEmpty("ziplist_exclusion"))
            );
            $returnObject["message"] .= ($fieldChecks["zip"]) ? "ZIP code allowed" : "ZIP code not allowed";
        } else {
            $returnObject["message"] .= "ZIP filter not set.";
            $fieldChecks["zip"] = true;
        }

        // Statelist check.
        if ($campaign->hasAttributeOrEmpty("statelist_exclusion") != "") {
            $fieldChecks["state"] = !in_array(
                $leadObject->hasPointOrEmpty("state"),
                explode(",", $campaign->hasAttributeOrEmpty("statelist_exclusion"))
            );
            $returnObject["message"] .= ($fieldChecks["state"]) ? "State allowed" : "State not allowed";
        } else {
            $returnObject["message"] .= "State filter not set.";
            $fieldChecks["state"] = true;
        }

        // Return the boolean.
        $returnObject["status"] = ($fieldChecks["state"] && $fieldChecks["zip"]);
        return $returnObject;
    }

    /**
     * @param $leadObject
     * @param $campaign
     * @return bool
     */
    private function blacklistCheck($leadObject, $campaign)
    {
        // Create an array to store the state of the blacklist checks.
        $blackListChecks = [
            "name" => false,
            "email" => false
        ];

        $returnObject = [
            "status" => false,
            "message" => ""
        ];

        // Name blacklist.
        if ($campaign->hasAttributeOrEmpty("name_blacklist") != "") {
            // Come up with the name to check against.
            $blackListChecks["name"] = !in_array(
                ($leadObject->first_name . " " . $leadObject->last_name),
                explode(",", $campaign->hasAttributeOrEmpty("name_blacklist"))
            );
            $returnObject["message"] .= ($blackListChecks["name"]) ? "Name is allowed" : "Name is on blacklist, blocked";
        } else {
            $returnObject["message"] .= "Name blacklist not set.";
            $blackListChecks["name"] = true;
        }

        // Email blacklist.
        if ($campaign->hasAttributeOrEmpty("blacklist_email") != "") {
            $blackListChecks["email"] = !in_array(
                $leadObject->email_address,
                explode(",", $campaign->hasAttributeOrEmpty("blacklist_email"))
            );
            $returnObject["message"] .= ($blackListChecks["name"]) ? "Email is allowed" : "Email is on blacklist, blocked";
        } else {
            $returnObject["message"] .= "Email blacklist not set.";
            $blackListChecks["email"] = true;
        }

        // Return the conditional of name&email.
        $returnObject["status"] = ($blackListChecks["name"] && $blackListChecks["email"]);
        return $returnObject;
    }

    /**
     * @param $leadObject
     * @param $campaign
     * @return bool
     */
    private function ageCheck($leadObject, $campaign)
    {
        // Return object.
        $returnObject = [];
        // Functionality for age check.
        if ($campaign->hasAttributeOrEmpty("has_age_filter") == "Yes") {
            if ($leadObject->hasPointOrEmpty("dob") != "") {
                // Current lead age.
                $age = $this->dateToAge($this->getCompiledDateStringFromString($leadObject->hasPointOrEmpty("dob")));
                if ($campaign->hasAttributeOrEmpty("age_to_range") == "") {
                    // XX+ campaigns (65+ campaigns)
                    $returnObject = [
                        "status" => ($age >= $campaign->hasAttributeOrEmpty("age_from_range")),
                        "message" => "User is above target age range."
                    ];
                } else {
                    // XX-YY campaigns (18-24 campaigns)
                    $returnObject = [
                        "status" => (
                            $age >= $campaign->hasAttributeOrEmpty("age_from_range") &&
                            $age <= $campaign->hasAttributeOrEmpty("age_to_range")
                        ),
                        "message" => "User is within target age range."
                    ];
                }
            } else {
                $returnObject = [
                    "status" => false,
                    "message" => "Send 'dob' field, this campaign accepts the 'MMDDYYY' format, for example, '12171987'."
                ];
            }
        } else {
            $returnObject = [
                "status" => true,
                "message" => "Age filter not set"
            ];
        }

        return $returnObject;
    }

    /**
     * Date to age calculate.
     * @param $date
     * @return mixed
     */
    private function dateToAge($date)
    {
        $originalDate = new \DateTime($date);
        $today = new \DateTime('today');
        return $originalDate->diff($today)->y;
    }

    /**
     * Format to time.
     * @param $input
     * @return int
     */
    private function formatToTime($input)
    {
        return strtotime($this->getCompiledDateStringFromString($input));
    }

    /**
     * Get compiled date string from string.
     * @param $str
     * @return string
     */
    private function getCompiledDateStringFromString($str)
    {
        return (substr($str, 0, 2) . "/" .
            substr($str, 2, 2) . "/" .
            substr($str, 4, 4));
    }

    /**
     * @param $leadObject
     * @param $campaign
     * @return bool
     */
    private function genderCheck($leadObject, $campaign)
    {
        // Return object.
        $returnObject = [];
        // Functionality
        if ($campaign->hasAttributeOrEmpty("has_gender_filter")) {
            if ($leadObject->hasPointOrEmpty("gender") != "") {
                $returnObject = [
                    "status" => (
                        $leadObject->hasPointOrEmpty("gender") == $campaign->hasAttributeOrEmpty("gender_filter")
                    ),
                    "message" => "User is defined gender."
                ];
            } else {
                $returnObject = [
                    "status" => false,
                    "message" => "Send 'gender' field, this campaign accepts only, '".$campaign->hasAttributeOrEmpty("gender_filter")."'."
                ];
            }
        } else {
            $returnObject = [
                "status" => false,
                "message" => "Gender filter is not set."
            ];
        }

        return $returnObject;
    }

    /**
     * @param $leadObject
     * @param $campaign
     * @return bool
     */
    private function datetimeCheck($leadObject, $campaign)
    {
        // TODO: Work on this at a later date.
        return [
            "status" => true,
            "message" => "DateTime Validation"
        ];
    }
}