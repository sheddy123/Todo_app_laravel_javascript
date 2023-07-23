<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Todo;

class TodoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $faker = Faker::create();

        foreach (range(1, 10) as $index) {
            Todo::create([
                'content' => $faker->sentence,
                'category' => $faker->randomElement(['coursework', 'exam']),
                'done' => $faker->boolean,
            ]);
        }
    }
}
