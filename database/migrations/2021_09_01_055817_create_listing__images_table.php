<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListingImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listing_images', function (Blueprint $table) {
            $table->increments('id')->primary();
			$table->string('image_url');
			$table->unsignedInteger('listing_id');
			$table->foreign('listing_id')->references('id')->on('experiences')->onDelete('cascade');
			$table->double('image_width')->nullable();
			$table->double('image_height')->nullable();
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
        Schema::dropIfExists('listing__images');
    }
}
