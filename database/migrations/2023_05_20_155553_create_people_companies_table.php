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
        Schema::create('people_companies', function (Blueprint $table) {
            $table->id();
            $table->string('corporate_name')->length(100)->nullable()
                ->comment('Nome razão social de pessoa juridica.');
            $table->string('fantasy_name')->length(100)->nullable()
                ->comment('Nome fantasia de pessoa juridica.');
            $table->unsignedBigInteger('id_people')->unique()
                ->comment('ID da tabela people, para saber de qual pessoa é essa pessoa juridica!');
            $table->string('state_registration')->length(9)->nullable()
                ->comment('Inscrição estadual pessoa juridica.');
            $table->string('municipal_registration')->length(11)->nullable()
                ->comment('Inscrição municipal pessoa jurídica.');
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
        Schema::dropIfExists('people_companies');
    }
};
