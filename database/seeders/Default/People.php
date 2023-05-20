<?php

namespace Database\Seeders\Default;

use Carbon\Carbon;
use App\Models\Person;
use Illuminate\Database\Seeder;

class People extends Seeder
{

    public function run()
    {
        $filtrerPerson = [
            'document' => '12973011000137'
        ];
        $newPerson = [
            'name' => 'Sub100 Inóveis',
            'document' => '12973011000137',
            'birth_date' => '01/01/2000 00:00:00'
        ];
        $person = new Person();
        $person->firstOrCreate($filtrerPerson, $newPerson);
    }
}
