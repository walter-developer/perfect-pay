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
        Schema::create('asaas_clients_cards', function (Blueprint $table) {
            $table->id();
            $table->string('alias', 50)->comment('Alias atribuido ao cartão pelo cliente!');
            $table->string('token_cache', 20)->comment('token de cahce do cartão do cliente asaas!');
            $table->unsignedBigInteger('id_client_asaas')->unique()
                ->comment('ID da tabela asaas_clients, para saber de qual cliente asaas é este cartão!');
            $table->foreign('id_client_asaas')->references('id')->on('asaas_clients');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asaas_clients_cards');
    }
};
