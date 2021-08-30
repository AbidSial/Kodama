<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProfileColumnFcmtoken extends Migration
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
        $table->string('fcmtoken')->unsigned()->nullable()->change();
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
         $table->string('fcmtoken')->unsigned()->nullable(false)->change();
    });
    }
}
