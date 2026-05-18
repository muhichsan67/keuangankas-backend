<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DebtFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'          => User::factory(),
            'source'           => $this->faker->randomElement(['KPR BCA', 'Kredit Motor Honda', 'Pinjaman Keluarga', 'KTA Mandiri']),
            'monthly_cost'     => $this->faker->randomFloat(2, 500000, 5000000),
            'monthly_deadline' => $this->faker->numberBetween(1, 28),
            'total_tenor'      => $this->faker->randomElement([12, 24, 36, 48, 60, 120]),
        ];
    }
}
