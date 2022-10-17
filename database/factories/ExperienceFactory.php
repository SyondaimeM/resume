<?php

namespace Database\Factories;

use App\Models\Experience;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Experience>
 */
class ExperienceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $exps = [];
        for ($i = 6; $i < 11; $i++) {
            Section::insert([
                'id' => $i,
                'title' => $this->faker->title(),
                'details' => $this->faker->realText(),
                'country' => $this->faker->country(),
                'city' => $this->faker->city(),
                'startDate' => $this->faker->dateTime(),
                'isActive' => $this->faker->boolean(),
                'endDate' => $this->faker->dateTime(),
                'isShown' => $this->faker->boolean(),
                'user_id' => 1
            ]);
            $exps[]= Experience::create([
                'companyName' => $this->faker->company(),
                'section_id' => $i,
            ]);
        }
        return $exps;
    }
}
