<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlatformReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_platform_reports', function (Blueprint $table) {
            $table->string('report_guid')->primary();
            $table->string('timestamp');
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
        Schema::dropIfExists('daily_platform_reports');
    }
}
