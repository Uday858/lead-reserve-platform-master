<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_attributes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("campaign_id")
                ->unsigned();
            $table->foreign("campaign_id")
                ->references("id")
                ->on("campaigns")
                ->onDelete("cascade");
            // ID to reference the `mutable_data_pairs` table.
            $table->string("name");
            $table->integer("storage_id");
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
        Schema::dropIfExists('campaign_attributes');
    }
}
