<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddResponseFromAdvertiserToApiTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('a_p_i_transactions', function (Blueprint $table) {
            $table->text('response_from_advertiser')->after('payload');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('a_p_i_transactions', function (Blueprint $table) {
            $table->dropColumn('response_from_advertiser');
        });
    }
}
