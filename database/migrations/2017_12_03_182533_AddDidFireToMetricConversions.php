<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDidFireToMetricConversions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('metric_conversions', function (Blueprint $table) {
            $table->boolean('pixel_fire')->after('campaign_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('metric_conversions', function (Blueprint $table) {
            $table->dropColumn('pixel_fire');
        });
    }
}
