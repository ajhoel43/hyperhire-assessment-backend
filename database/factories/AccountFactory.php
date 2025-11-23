<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->name();

        $nameArr = explode(' ', $name);
        $firstLetter = substr($nameArr[0], 0, 1);
        $email = strtolower($firstLetter. '.' . $nameArr[1].'@example.com');
        $age = fake()->numberBetween(18, 60);
        $pictures = 'https://i.pravatar.cc/500?u='.$email;
        $location = fake()->city().', '.fake()->state();

        return [
            'name' => $name,
            'email' => $email,
            'age' => $age,
            'pictures' => $pictures,
            'location' => $location,
        ];
    }
}
