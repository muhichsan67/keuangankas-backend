<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'debt_id'     => null,
            'type'        => $this->faker->randomElement(['in', 'out']),
            'amount'      => $this->faker->randomFloat(2, 50000, 5000000),
            'category'    => $this->faker->randomElement(['Gaji', 'Bonus', 'Cicilan KPR', 'Belanja', 'Tagihan Listrik']),
            'date'        => $this->faker->dateThisYear(),
            'description' => $this->faker->optional()->sentence(),
            'receipt_url' => null,
        ];
    }
}
