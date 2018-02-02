<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignPostingParametersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_posting_parameters', function (Blueprint $table) {
            $table->increments('id');

            // For campaigns.
            $table->integer("campaign_id")->unsigned();
            $table->foreign("campaign_id")->references("id")->on("campaigns");

            // For field type.
            $table->string("type");

            // Incoming field identifier.
            $table->string("incoming_field");

            // Outgoing field identifier.
            $table->string("outgoing_field");

            // Label for field.
            $table->string("label");

            // Static?
            $table->boolean("is_static")->default(false);
            $table->string("static_value")->default("");
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
        Schema::dropIfExists('campaign_posting_parameters');
    }
}
