<?php

namespace Database\Factories;

use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeacherFactory extends Factory
{
    protected $model = Teacher::class;

    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'employee_id' => 'TCH/' . $this->faker->unique()->numerify('#####'),
            'specialty' => $this->faker->randomElement(['Mathematics', 'English', 'Science', 'History', 'Geography']),
        ];
    }
}
