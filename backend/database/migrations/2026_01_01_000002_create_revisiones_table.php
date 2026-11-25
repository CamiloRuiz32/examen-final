<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revisiones', function (Blueprint $table) {
            $table->bigInteger('id', autoIncrement: true);
            $table->string('codigo', 20)->unique();
            $table->boolean('aceite');
            $table->boolean('filtro');
            $table->boolean('frenos');
            $table->bigInteger('coche_id');
            $table->foreign('coche_id')->references('id')->on('coches')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revisiones');
    }
};
