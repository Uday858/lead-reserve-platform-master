<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePublisherCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publisher_campaigns', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("publisher_id")
                ->unsigned();
            $table->foreign("publisher_id")
                ->references("id")
                ->on("publishers")
                ->onCascade("delete");
            $table->integer("campaign_id")
                ->unsigned();
            $table->foreign("campaign_id")
                ->references("id")
                ->on("campaigns")
                ->onCascade("delete");
            $table->float("payout");
            $table->integer("lead_cap");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('publisher_campaigns');
    }
}
