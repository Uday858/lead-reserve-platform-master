<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadMetricsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_metrics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('campaign_id')->nullable();
            $table->integer('publisher_id')->nullable();
            $table->integer('lead_id')->nullable();
            $table->decimal('buy')->nullable();
            $table->decimal('sell')->nullable();
            $table->decimal('margin')->nullable();
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
        Schema::dropIfExists('lead_metrics');
    }
}
