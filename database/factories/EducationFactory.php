<?php

namespace Database\Factories;

use App\Models\Education;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class EducationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $edus = [];
        for ($i = 1; $i < 4; $i++) {
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
            $edus[]= Education::create([
                    'collageName' => $this->faker->name(),
                    'degree' => $this->faker->name(),
                    'department' => $this->faker->domainName(),
                    'gpa' => 3,
                    'section_id' => $i,
                ]);
        }
        return $edus;
    }
}
