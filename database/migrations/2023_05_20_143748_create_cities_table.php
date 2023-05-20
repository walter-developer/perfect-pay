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
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->length(60)
                ->comment('Nome da cidade.');
            $table->string('acronym')->length(10)
                ->nullable()->comment('Sigla da cidade');
            $table->string('ibge')->length(7)->nullable()
                ->comment('Código ibge da cidade');
            $table->string('ddd')->length(3)->nullable()
                ->comment('Código ddd da cidade');
            $table->unsignedBigInteger('id_states')
                ->comment('ID da tabela estados, para saber de qual estado é essa cidade!');
            $table->foreign('id_states')->references('id')->on('states');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
