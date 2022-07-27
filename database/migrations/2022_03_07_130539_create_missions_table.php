<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('missions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('vehicule_id');
            $table->string('adress');
            $table->string('tel_Dest')->nullable();
            $table->string('chargement')->nullable();
            $table->string('etat')->default('En attente');
            $table->text('description')->nullable();
           //$table->string('longitude');
           //$table->string('latitude');
           $table->float('distanceparcouru')->default(1);
            $table->date('date');
            $table->time('arriveTime');
            $table->time('departTime');

            $table->timestamps();
           
            $table->foreign('user_id')->references('id')->on('users')
            ->onUpdate('cascade')
            ->onDelete('cascade');
            $table->foreign('vehicule_id')->references('id')->on('vehicules')
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('missions');
    }
    
}
