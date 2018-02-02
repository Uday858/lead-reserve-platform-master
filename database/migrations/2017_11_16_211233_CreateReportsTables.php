<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Generated campaign reports
        Schema::create('generated_campaign_reports', function (Blueprint $table) {
            $table->string('report_guid')->primary();
            $table->string('timestamp');
            $table->integer('campaign_id')->unsigned();
            $table->foreign('campaign_id')->references('id')->on('campaigns');
            $table->integer('leads_generated')->nullable();
            $table->integer('leads_accepted')->nullable();
            $table->integer('leads_rejected')->nullable();
            $table->integer('metric_impressions')->nullable();
            $table->integer('metric_clicks')->nullable();
            $table->integer('metric_conversions')->nullable();
            $table->decimal('revenue')->nullable();
            $table->decimal('payout')->nullable();
            $table->decimal('margin')->nullable();
            $table->text('cache_data')->nullable();
            $table->timestamps();
        });
        // Generated campaign reports
        Schema::create('generated_campaign_publisher_reports', function (Blueprint $table) {
            $table->string('report_guid')->primary();
            $table->string('timestamp');
            $table->integer('campaign_id')->unsigned();
            $table->foreign('campaign_id')->references('id')->on('campaigns');
            $table->integer('publisher_id')->unsigned();
            $table->foreign('publisher_id')->references('id')->on('publishers');
            $table->integer('leads_generated')->nullable();
            $table->integer('leads_accepted')->nullable();
            $table->integer('leads_rejected')->nullable();
            $table->integer('metric_impressions')->nullable();
            $table->integer('metric_clicks')->nullable();
            $table->integer('metric_conversions')->nullable();
            $table->decimal('revenue')->nullable();
            $table->decimal('payout')->nullable();
            $table->decimal('margin')->nullable();
            $table->text('cache_data')->nullable();
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
        //
        Schema::dropIfExists('generated_campaign_reports');
        Schema::dropIfExists('generated_campaign_publisher_reports');
    }
}
