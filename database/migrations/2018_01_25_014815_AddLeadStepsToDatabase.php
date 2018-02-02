<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\LeadStatusStep;

class AddLeadStepsToDatabase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        LeadStatusStep::create([
            "step_name" => "accepted"
        ]);
        LeadStatusStep::create([
            "step_name" => "test"
        ]);
        LeadStatusStep::create([
            "step_name" => "rejected"
        ]);
        LeadStatusStep::create([
            "step_name" => "failure"
        ]);
        LeadStatusStep::create([
            "step_name" => "advertiser_error"
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
