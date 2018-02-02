<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMutableDataPairsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mutable_data_pairs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->default(-1);
            $table->string('parent_type')->default("setting");
            $table->string('data_description')->default("Platform Default");
            $table->enum("type",[
                "integer","float","bool","string","json"
            ]);
            $table->integer("integer_value")->default(0);
            $table->float("float_value")->default(0);
            $table->boolean("bool_value")->default(false);
            $table->string("string_value")->default("");
            $table->text("json_value")->nullable();
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
        Schema::dropIfExists('mutable_data_pairs');
    }
}
