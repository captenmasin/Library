<?php

namespace Database\Factories;

use App\Models\Publisher;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class PublisherFactory extends Factory
{
    protected $model = Publisher::class;

    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid(),
            'name' => $this->faker->name().'-'.rand(0, 99999),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
