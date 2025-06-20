<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition(): array
    {
        return [
            'persistSettings'                            => $this->faker->boolean(),
            'settings'                                   => $this->faker->words(),
            'settingsFieldName'                          => $this->faker->name(),
            'registerMediaConversionsUsingModelInstance' => $this->faker->boolean(),
            'defaultSettings'                            => $this->faker->words(),
            'settingsRules'                              => $this->faker->words(),
        ];
    }
}
