<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('prenom')->default('0');
            $table->date('datenaiss')->nullable();
            $table->string('cin')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('tel')->default('0');
            $table->string('adress')->default('0');
            $table->string('usertype')->default('0');
            $table->string('dispochau')->default('0');
            $table->string('image')->default('0');
            $table->string('imagepath')->default('0');
            $table->string('numpermis')->default('0');
            $table->date('datesortie')->nullable();
            $table->date('datefin')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
