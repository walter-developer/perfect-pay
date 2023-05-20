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
        Schema::table('people', function (Blueprint $table) {
            $table->foreign('id_people_users')->references('id')->on('people_users');
            $table->foreign('id_people_contacts')->references('id')->on('people_contacts');
            $table->foreign('id_people_adresses')->references('id')->on('people_adresses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('people', function (Blueprint $table) {
            $table->dropForeign(['id_people_users']);
            $table->dropForeign(['id_people_contacts']);
            $table->dropForeign(['id_people_adresses']);
        });
    }
};
