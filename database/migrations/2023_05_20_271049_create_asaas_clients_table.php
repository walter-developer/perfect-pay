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
        Schema::create('asaas_clients', function (Blueprint $table) {
            $table->id();
            $table->string('id_client_asaas', 20);
            $table->unsignedBigInteger('id_people')->unique()
                ->comment('ID da tabela people, para saber de qual pessoa é essa pessoa fisica!');
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
        Schema::dropIfExists('asaas_clients');
    }
};
