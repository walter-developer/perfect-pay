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
        Schema::create('asaas_clients_charges', function (Blueprint $table) {
            $table->id();
            $table->string('id_charge_asaas', 20)->nullable()->comment('ID Cliente Asaas!');
            $table->smallInteger('charge_type')->nullable()->comment('Tipo da cobrança!');
            $table->smallInteger('charge_status')->nullable()->comment('Status da cobrança!');
            $table->date('due_date')->nullable()->comment('Data de vencimento da cobrança.');
            $table->decimal('value', 9, 0)->nullable()->comment('Valor da cobrança.');
            $table->text('description')->nullable()->comment('Descrição da cobança do lado de cadastrante');
            $table->unsignedBigInteger('id_client_asaas')->comment('ID da tabela asaas_clients, para saber de qual cliente asaas é esta cobrança!');
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
        Schema::dropIfExists('asaas_clients_charges');
    }
};
