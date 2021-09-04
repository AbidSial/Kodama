<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->increments('location_id')->primary();
			$table->string('location_name');
			$table->string('street_address1');
			$table->string('street_address2');
			$table->double('lat')->nullable();
			$table->double('long')->nullable();
			$table->string('city');
			$table->string('state');
			$table->string('zip');
			$table->boolean('isDefault')->default(false);
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
        Schema::dropIfExists('locations');
    }
}
