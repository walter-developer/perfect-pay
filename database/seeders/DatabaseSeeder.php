<?php

namespace Database\Seeders;

use Throwable, Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::transaction(function () {
            try {
                $this->call([
                    \Database\Seeders\Default\People::class,
                    \Database\Seeders\Default\PeopleUsers::class,
                ]);
                DB::commit();
            } catch (Throwable $e) {
                DB::rollBack();
                throw new Exception($e->getMessage());
            }
        });
    }
}
