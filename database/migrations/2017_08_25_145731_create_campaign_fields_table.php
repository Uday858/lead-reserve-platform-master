<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_fields', function (Blueprint $table) {
            // For reference..
            $table->increments('id');
            $table->integer("campaign_id");

            // We have to typecast and label the field.
            $table->string("type")->nullable();
            $table->string("label")->nullable();

            // Still used for translation.
            $table->string("incoming_field")->nullable();
            $table->string("outgoing_field")->nullable();

            // All of the values.
            $table->string("hardcoded_value")->nullable();
            $table->string("system_value")->nullable();
            $table->string("inclusion_value")->nullable();
            $table->string("random_value")->nullable();

            // The posting spec caption.
            $table->string("spec_caption")->nullable();

            // CreatedAt/UpdatedAt
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
        Schema::dropIfExists('campaign_fields');
    }
}
