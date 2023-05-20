<?php

namespace Database\Seeders\Default;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\{
    Person,
    PersonUser
};

class PeopleUsers extends Seeder
{

    public function run()
    {
        $user = new PersonUser();
        $person  = (new Person())?->where('document', '12973011000137')?->get() ?: new Collection();
        if ($person?->count() && empty($user->first())) {
            $newPersonUser = [
                'id_people' => $person?->first()?->id,
                'email' => 'sub100@sub100.com.br',
                'password' =>  Hash::make('sub100')
            ];
            $user->firstOrCreate($newPersonUser, $newPersonUser);
        }
    }
}
