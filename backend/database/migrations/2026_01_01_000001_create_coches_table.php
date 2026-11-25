<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coches', function (Blueprint $table) {
            $table->bigInteger('id', autoIncrement: true);
            $table->string('marca', 80);
            $table->string('modelo', 80);
            $table->string('color', 40);
            $table->decimal('precio', 12, 2);
            $table->string('foto', 255)->nullable();
            $table->bigInteger('cliente_id');
            $table->foreign('cliente_id')->references('id')->on('clientes')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coches');
    }
};
