<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'admission_number' => 'STU/' . $this->faker->unique()->numerify('#####'),
            'class_id' => null,
            'date_of_birth' => $this->faker->date('Y-m-d', '-10 years'),
            'gender' => $this->faker->randomElement(['male', 'female']),
        ];
    }
}
