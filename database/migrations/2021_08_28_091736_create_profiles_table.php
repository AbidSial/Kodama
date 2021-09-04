<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->increments('profile_id')->primary();
            $table->string('full_name');
			$table->string('reservation_website');
			$table->string('business_name');
			$table->string('street_address');
			$table->string('image_url');
			$table->string('profile_bio');
		    $table->string('fcm_token');
			$table->unsignedInteger('user_id');
  $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('profiles');
    }
}
