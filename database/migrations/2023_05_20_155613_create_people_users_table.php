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
        Schema::create('people_users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique()->length(100)->nullable()
                ->comment('E-mail de autenticaçãod e um usuário ao sistema.');
            $table->string('password')->length(255)
                ->comment('Campo de senha usado para autenticar no sistema.');
            $table->unsignedBigInteger('id_people')
                ->comment('ID da tabela people, para saber de qual pessoa é esse usuário!');
            $table->foreign('id_people')->references('id')->on('people');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people_users');
    }
};
