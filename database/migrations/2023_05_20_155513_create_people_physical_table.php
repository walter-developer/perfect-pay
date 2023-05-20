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
        Schema::create('people_physical', function (Blueprint $table) {
            $table->id();
            $table->string('general_record')->length(15)
                ->comment('RG da entidade pessoa para pessoas fisícas.');
            $table->string('father_name')->length(100)->nullable()
                ->comment('Nome do pai da entidade pessoa em caso de pessoa fisíca.');
            $table->string('mother_name')->length(100)->nullable()
                ->comment('Nome da mãe da entidade pessoa em caso de pessoa fisíca.');
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
        Schema::dropIfExists('people_physical');
    }
};
