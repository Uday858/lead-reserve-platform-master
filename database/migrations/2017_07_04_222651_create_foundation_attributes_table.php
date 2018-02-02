<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFoundationAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('foundation_attributes', function (Blueprint $table) {
            $table->increments("id");
            $table->integer("foundation_parent_id")
                ->unsigned();
            // ID to reference the `mutable_data_pairs` table.
            $table->string("name")->nullable();
            $table->integer("storage_id")->nullable();
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
        Schema::dropIfExists('foundation_attributes');
    }
}
