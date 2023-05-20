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
        Schema::create('people_contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_people')
                ->comment('ID da tabela people, para saber de qual pessoa é esse contato!');
            $table->foreign('id_people')->references('id')->on('people');
            $table->unsignedBigInteger('id_contacts')
                ->comment('ID da tabela contacts, para saber de qual contato é de uma pessoa!');
            $table->foreign('id_contacts')->references('id')->on('contacts');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people_contacts');
    }
};
