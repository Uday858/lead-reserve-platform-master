<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("advertiser_id")
                ->unsigned();
            $table->foreign("advertiser_id")
                ->references("id")
                ->on("advertisers")
                ->onDelete("cascade");
            $table->integer("campaign_type_id");
            // Name for campaign & Delivery/Posting URL.
            $table->string("name");
            $table->string("posting_url");
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
        Schema::dropIfExists('campaigns');
    }
}
