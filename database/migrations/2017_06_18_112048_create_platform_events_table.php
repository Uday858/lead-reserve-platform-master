<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlatformEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('platform_events', function (Blueprint $table) {
            $table->increments('id');
            $table->string("name");
            $table->string("description")->default("");
            $table->enum("type",[
                "integer","float","bool","string","json"
            ]);
            $table->integer("integer_value")->default(0);
            $table->float("float_value")->default(0);
            $table->boolean("bool_value")->default(false);
            $table->string("string_value")->default("");
            $table->json("json_value");
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
        Schema::dropIfExists('platform_events');
    }
}
