<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DeveloperToolsController extends Controller
{
    public function getCacheDebuggerTester()
    {
        // data
        $cacheData = []; // for campaign data.
        // get live campaign ids
        foreach($this->fetchAllLiveCampaignIds() as $campaignId) {
            $cacheData[] = Cache::get("campaign.".$campaignId.".data");
        }
        dd($cacheData);
    }

    private function fetchAllLiveCampaignIds() {
        // todo: flatten this bitch.
        $data = DB::select('SELECT * FROM lead_reserve_platform.view_active_campaign_ids;'); 
        return collect($data)->map(function($x){ return $x->campaign_id; })->toArray();
    }

    public function getQueueDebuggerTester()
    {
        return view('developer_tools.queuetester',[
            "platformProcessing" => Redis::connection()->llen('queues:platform-processing'),
            "leadProcessing" => Redis::connection()->llen('queues:lead-processing'),
            "reportProcessing" => Redis::connection()->llen('queues:report-processing'),
            "agedPlatformProcessing" => Redis::connection()->llen('queues:additional-platform-processing'),
            "leadSendingProcessing" => Redis::connection()->llen('queues:lead-sending-processing')
        ]);
    }

    public function getRegularExpressionTester()
    {
        return view('developer_tools.regexpatterntester');
    }

    public function debugRegularExpression()
    {
        if(Input::get("contents")) {
            $parsedResponseContents = $this->parseResponseContents(
                Input::get("contents"),
                base64_decode(Input::get("pattern")),
                Input::get("selection")
            );
            return $parsedResponseContents;
        }

        return [
            "status" => "fail",
            "message" => "No payload provded."
        ];
    }

    /**
     * @param $contents
     * @param $pattern
     * @param $selectNumber
     * @return mixed
     */
    private function parseResponseContents($contents,$pattern,$selectNumber) {
        // Create the matches array.
        $matches = [];

        // Regular expression match.
        preg_match_all($pattern,$contents,$matches);

        if(count(explode(",",$selectNumber))!==1) {
            $searchMatchesArray = explode(",",$selectNumber);
            return $matches[$searchMatchesArray[0]][$searchMatchesArray[1]];
        }

        // Return the selected match number.
        return $matches[0][$selectNumber];
    }
}
