<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToAPITransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('a_p_i_transactions', function (Blueprint $table) {
            $table->string('lead_status')->after('lead_id')->nullable();
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
            $table->dropColumn('lead_status');
        });
    }
}
