<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('people_adresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_people')
                ->comment('ID da tabela people, para saber de qual pessoa é vinculada a esse endereço!');
            $table->foreign('id_people')->references('id')->on('people');
            $table->unsignedBigInteger('id_adresses')
                ->comment('ID da tabela endereços, para saber de qual endereço é vinculado a uma pessoa!');
            $table->foreign('id_adresses')->references('id')->on('adresses');
            $table->string('number')->length(10)
                ->comment('Numero do endereço');
            $table->string('observation')->length(200)->nullable()
                ->comment('observacao pra o endereço, exemplo: casa numero ( 100A ) ou (100 B )');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people_adresses');
    }
};
