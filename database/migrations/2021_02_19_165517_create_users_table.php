<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->down();
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('document', 14);
            $table->string('email', 100);
            $table->string('password', 100);
            $table->integer('type_user');
            $table->decimal('balance', 15, 2)->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
