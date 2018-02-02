<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAPITransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('a_p_i_transactions', function (Blueprint $table) {
            // Required
            $table->increments('id');
            $table->string('transaction_id');
            $table->string('ip_address');
            $table->decimal('transaction_time');
            $table->string('full_request_url');
            // Not Required
            $table->integer('lead_id')->nullable();
            $table->string('pid')->nullable();
            $table->string('cid')->nullable();
            $table->string('payload')->nullable();
            $table->string('error_message')->nullable();
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
        Schema::dropIfExists('a_p_i_transactions');
    }
}
