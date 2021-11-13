<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateFollowersTable extends Migration
{
    public function up()
    {
        Schema::create(config('followers.tables.followers'), function (Blueprint $table) {
            $table->increments('id');
            $table->uuidMorphs('sender');
            $table->uuidMorphs('recipient');
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists(config('followers.tables.followers'));
    }
}
