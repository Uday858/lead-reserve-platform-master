<?php

namespace App\Http\Controllers;

use App\Lead;
use App\Campaign;
use App\Publisher;
use App\PlatformEvent;
use App\APITransaction;
use App\Providers\LeadMoldingProvider;
use App\Providers\PlatformEventHandlerServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;

class LeadExplorerController extends Controller
{

    public function transactionHistory($transactionId) {
        if(APITransaction::where("transaction_id",$transactionId)->exists()) {
            return redirect('dashboard/lead-explorer-history/'.APITransaction::where("transaction_id",$transactionId)->first()->lead_id);
        } else {
            return view('errors.404');
        }
    }

    public function index() {

        // input variables: lead_id, campaign_id, publisher_id, from_date, to_date

        if(Input::get("lead_id") == "") {
           /* $leads = Lead::where("campaign_id",Input::get("campaign_id"))
                ->where("publisher_id",Input::get("publisher_id"))
                ->where("created_at",">=",Input::get("from_date"))
                ->where("created_at","<=",Input::get("to_date"))
                ->limit(20)
                ->get();*/
            $leads = Lead::where("campaign_id",Input::get("campaign_id"))
                ->where("publisher_id",Input::get("publisher_id"))
                ->where("created_at",">=",Input::get("from_date"))
                ->where("created_at","<=",Input::get("to_date"));
            $totalCount = 0;
            $currentPage = 0;
            $lastPage = 0;
            $perPage = 20;
            $totalCount = $leads->count();
            $currentPage = Input::get("page") ? Input::get("page") : 0;
            $lastPage = floor($totalCount / $perPage);
            $leads = $leads->skip($currentPage * $perPage)->limit($perPage)->get();

            return view('lead_explorer.lead_view',[
                "leads" => $leads,
                "campaign" => Campaign::whereId(Input::get("campaign_id"))->first(),
                "publisher" => Publisher::whereId(Input::get("publisher_id"))->first(),
                "lastPage" => $lastPage,
                "currentPage" => $currentPage
            ]);
        } else {
            return $this->specificLeadHistory(Input::get("lead_id"));
        }
    }

    public function specificLeadHistory($leadId) {
        // get status, request, response.
        return $this->getFullLeadInformation($leadId);
    }

    public function getLeadStatus($leadId) {
        $queryObject = DB::table("view_lead_statuses")->where(DB::raw("cast(lead_id as unsigned)"),$leadId)->first();
        if($queryObject != null) {
            return ($queryObject->is_accepted ? "accepted" : ($queryObject->is_test ? "test" : ($queryObject->is_rejected ? "rejected" : "notsent")));
        } else {
            return 'invalid';
        }
    }
    public function getLeadImageURL($leadEmailAddress) {
        return md5(strtolower(trim($leadEmailAddress)));
    }
    
    public function getFullLeadInformation($leadId) {
        if(Lead::whereId($leadId)->exists()) {
            $lead = Lead::whereId($leadId)->first();
            $platformEvents = PlatformEvent::where(DB::raw('cast(json_value->"$.lead_id" as unsigned)'),$leadId)->get();

            // Create a blank array for view data.
            $viewData = [
                'lead_id' => $leadId,
                'lead' => $lead,
                'campaign' => Campaign::whereId($lead->campaign_id)->first(),
                'publisher' => Publisher::whereId($lead->publisher_id)->first()
            ];

            if(APITransaction::where("lead_id",$leadId)->exists()) {
                $viewData['transactionData'] = APITransaction::where("lead_id",$leadId)->first();
            }

            foreach($platformEvents as $event) {
                // remove the '.' from the lead platform name.
                $eventName = str_replace('.','',$event->name);
                $viewData[$eventName] = call_user_func_array([$this,'process' . $eventName],[$event]);
            }

            return view('lead_explorer.detailed_lead_history',$viewData);

        } else {
            return view('errors.404',[
                "error_message" => "Lead does not exist in the system. Try loading again from a different page, or, check for typo(s)."
            ]);
        }
        
        /*$leads = DB::table("view_full_lead_explorer_values")
                ->where("lead_id",$leadId)
                ->get()
                ->groupBy("lead_id");
        $formattedLeadView = [];
        foreach($leads as $lead) {
            $formattedLeadView[] = [
                "lead_id" => $lead[0]->lead_id,
                "request" => $lead[0]->request,
                "response" => $lead[1]->response,
                "status" => ($lead[0]->is_accepted ? "Accepted" : ($lead[0]->is_test ? "Test" : ($lead[0]->is_rejected ? "Rejected" : "Not Sent")))
            ];
        }
        if(isset($formattedLeadView[0])) {
            return view('lead_explorer.detailed_lead_history',$formattedLeadView[0]);
        } else {
            return view('lead_explorer.detailed_lead_history',[
                'lead_id' => $leadId,
                'request' => "",
                'response' => "",
                'status' => "Undefined",
                "error_message" => "Lead request/response could not be found. (todo: figure out why... lol)"
            ]);
        }*/
    }

    private function processLeadValidation($event) {
        $eventJSON = json_decode($event->json_value,1);
        return [
            "age" => $eventJSON["validations"]["age"]["status"],
            "gender" => $eventJSON["validations"]["gender"]["status"],
            "datetime" => $eventJSON["validations"]["datetime"]["status"],
            "blacklist" => $eventJSON["validations"]["blacklist"]["status"],
            "fieldExclusion" => $eventJSON["validations"]["fieldExclusion"]["status"]
        ];
    }

    private function processLeadPresend($event) {
        $eventJSON = json_decode($event->json_value,1);
        return [
            "request" => $eventJSON["requestURL"],
            "warning_message" => "Lead request for delivery."
        ];
    }

    private function processLeadSent($event) {
        $eventJSON = json_decode($event->json_value,1);
        return [
            "response" => $eventJSON["response_data"]["contents"],
            "warning_message" => "Lead response has been processed. That means that the server had sent a successful request."
        ];
    }

    private function processLeadAccepted($event) {
        $eventJSON = json_decode($event->json_value,1);
        return [
            "success_message" => "Lead was accepted by advertiser."
        ];
    }

    private function processLeadRejected($event) {
        $eventJSON = json_decode($event->json_value,1);
        return [
            "reason" => $eventJSON["reason"],
            "flow_state" => $eventJSON["flow_state"],
            "created_at" => $event->created_at,
            "error_message" => "Lead was rejected by advertiser."
        ];
    }

    private function processRevenueTrack($event) {
        $eventJSON = json_decode($event->json_value,1);
        return [
            "cpl" => $eventJSON["cpl"],
            "net" => $eventJSON["net"],
            "payout" => $eventJSON["payout"],
            "success_message" => "Lead finances were tracked accurately."
        ];
    }

    /*{"lead_id": 907708, "allowedValues": "M,F", "attemptedValue": "1"}*/
    private function processLeadSentFailed($event) {
        $eventJSON = json_decode($event->json_value,1);
        return [
            "allowedValues" => $eventJSON["allowedValues"],
            "attemptedValue" => $eventJSON["attemptedValue"],
            "error_message" => "Lead was not delivered to advertiser."
        ];
    }

    /*TODO: Build these*/
    private function processLeadTest($event) {
        dd($event);
    }
    private function processLeadCaptured($event) {
        dd($event);
    }


    /**
     * Build out lead query.
     * @return Lead|\Illuminate\Database\Query\Builder|null
     */
    private function buildLeadQuery() {

        $leadQuery = null;

        if(Input::get("campaign_id") != "") {
            $leadQuery = Lead::whereCampaignId(Input::get("campaign_id"));
        }
        if(Input::get("publisher_id") != "") {
            if($leadQuery != null) {
                $leadQuery->wherePublisherId(Input::get("publisher_id"));
            } else {
                $leadQuery = Lead::wherePublisherId(Input::get("publisher_id"));
            }
        }
        if(Input::get("from_date") != "") {
            if($leadQuery != null) {
                $leadQuery->where("created_at",">=",Input::get("from_date"));
            } else {
                $leadQuery = Lead::where("created_at",">=",Input::get("from_date"));
            }
        }
        if(Input::get("to_date") != "") {
            if($leadQuery != null) {
                $leadQuery->where("created_at","<=",Input::get("to_date"));
            } else {
                $leadQuery = Lead::where("created_at","<=",Input::get("to_date"));
            }
        }

        return $leadQuery;
    }
}
