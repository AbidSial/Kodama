<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProfileMakeColumnNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::table('profiles', function($table) {
        $table->string('business_name')->unsigned()->nullable()->change();
		$table->string('reservation_website')->unsigned()->nullable()->change();
		$table->string('street_address')->unsigned()->nullable()->change();
		$table->string('profile_bio')->unsigned()->nullable()->change();
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
		Schema::table('profiles', function($table) {
        $table->string('business_name')->unsigned()->nullable()->change();
		$table->string('reservation_website')->unsigned()->nullable()->change();
		$table->string('street_address')->unsigned()->nullable()->change();
		$table->string('profile_bio')->unsigned()->nullable()->change();
    });
    }
}
