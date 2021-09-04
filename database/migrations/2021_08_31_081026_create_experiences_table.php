<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExperiencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('experiences', function (Blueprint $table) {
             $table->increments('id')->primary();
			$table->string('item_title');
			$table->string('item_description');
			$table->string('reservation_website');
			$table->unsignedInteger('location_id');
			$table->foreign('location_id')->references('location_id')->on('locations')->onDelete('cascade');
			$table->double('item_price');
			$table->string('price_category');
			$table->unsignedInteger('user_id');
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('experiences');
    }
}
