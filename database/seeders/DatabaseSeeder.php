<?php

namespace Database\Seeders;

use App\Models\Education;
use App\Models\Experience;
use App\Models\Section;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use phpDocumentor\Reflection\Types\Integer;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create();
        Education::factory()->make();
        Experience::factory()->make();
        Skill::insert([
                'name' => 'PHP',
                'description' => 'Programming Language.',
                'isShown' => 1,
                'user_id' => 1
        ]);
        Skill::insert([
                'name' => 'C#',
                'description' => 'Programming Language.',
                'isShown' => 0,
                'user_id' => 1
        ]);
        Skill::insert([
                'name' => 'Visual Studio',
                'description' => 'Programming IDE.',
                'isShown' => 1,
                'user_id' => 1
        ]);
        Skill::insert([
                'name' => 'Agile Development &amp; Scrum',
                'description' => 'Programming Method.',
                'isShown' => 1,
                'user_id' => 1
        ]);
        // \App\Models\User::factory(10)->create();

//         \App\Models\User::factory()->create([
//             'name' => 'Sanish',
//             'email' => 'SanishMaharjan@gmail.com',
//         ]);
    }
}
