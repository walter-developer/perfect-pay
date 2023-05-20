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
        Schema::create('adresses', function (Blueprint $table) {
            $table->id();
            $table->string('cep')->length(8)->nullable()
                ->comment('Cep do endereço');
            $table->string('address')->length(200)
                ->comment('Endereço/Rua no bairro');
            $table->string('complement')->length(200)->nullable()
                ->comment('Observação para Endereço/Rua atual');
            $table->unsignedBigInteger('id_neighborhoods')
                ->comment('ID da tabela bairros, para saber de qual bairro é esse endereço!');
            $table->foreign('id_neighborhoods')->references('id')->on('neighborhoods');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adresses');
    }
};
