<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiculesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicules', function (Blueprint $table) {
            $table->id();
            $table->string('marque')->default('0');
            $table->string('matricule')->unique();
            $table->string('modele')->default('0');
            $table->string('couleur')->default('0');
            $table->string('puissance')->default('0');
            $table->string('datepremiere')->default('0');
            $table->string('dateentre')->default('0');
            $table->string('dispovehi')->default('0');
            $table->string('energie')->default('0');
            $table->float('maxreservoire')->default(0);
            $table->float('consomation_moy')->default(0);
            $table->float('pneuxKM')->default(50000);
            $table->float('vidangeKM')->default(15000);
            $table->float('kmparcouru')->default(1);
            $table->date('dernierAssurace');
            $table->date('dernierVisiteTechnique');
            $table->date('dateexpirationassurance');
            $table->date('dateexpirationvisitetech');
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
        Schema::dropIfExists('vehicules');
    }
}
