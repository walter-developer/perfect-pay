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
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('name')->length(150)
                ->comment('Nome da entidade pessoa, podendo ser nome, ou fantasia, ou alias, será usado como nome da conta.');
            $table->string('document')->unique()->length(20)
                ->comment('Cpf ou Cnpj da entidade pessoa.');
            $table->timestamp('birth_date')
                ->comment('Data de nascimento da entidade pessoa.');
            $table->unsignedBigInteger('id_people_users')->nullable()
                ->comment('ID da tabela people_users, para saber qual usuário é o principal!');
            $table->unsignedBigInteger('id_people_contacts')->nullable()
                ->comment('ID da tabela people_contacts, para saber qual contato é o principal!');
            $table->unsignedBigInteger('id_people_adresses')->nullable()
                ->comment('ID da tabela people_adresses, para saber qual endereço é o principal!');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
