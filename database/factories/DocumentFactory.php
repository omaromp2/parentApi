<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'original_name' => $this->faker->filePath(),
            'path'          => 'paystubs/'.$this->faker->uuid().'.pdf',
            'status'        => 'uploaded',
            'extracted'     => null,
            'summary'       => null,
            'error'         => null,
        ];
    }
}
