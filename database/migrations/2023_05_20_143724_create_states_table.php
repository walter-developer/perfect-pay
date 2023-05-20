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
        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->string('name')->length(60)
                ->comment('Nome do estado.');
            $table->string('acronym')->length(4)
                ->nullable()->comment('Sigla do estado');
            $table->unsignedBigInteger('id_countries')
                ->comment('ID da tabela paises, para saber de qual país é esse estado!');
            $table->foreign('id_countries')->references('id')->on('countries');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('states');
    }
};
