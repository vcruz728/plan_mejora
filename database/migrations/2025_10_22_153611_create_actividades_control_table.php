<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('actividades_control', function (Blueprint $table) {
            $table->increments('id');           // INT
            $table->integer('id_plan');         // INT (en SQL Server no hay unsigned)
            $table->string('actividad', 500);
            $table->string('producto_resultado', 500);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->string('responsable', 200);
            $table->bigInteger('id_usuario')->nullable(); // puede ser BIGINT, no afecta
            $table->timestamps();
        });

        Schema::table('actividades_control', function (Blueprint $table) {
            $table->foreign('id_plan')->references('id')->on('mejoras')->cascadeOnDelete();
        });
    }
    public function down()
    {
        Schema::dropIfExists('actividades_control');
    }
};
