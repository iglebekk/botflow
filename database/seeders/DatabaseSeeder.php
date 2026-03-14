<?php

namespace Database\Seeders;

use App\Models\Bot;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@test.test',
            'password' => bcrypt('password'),
        ]);

        Task::factory(10)->create([
            'creator_id' => $user->id,
            'creator_type' => get_class($user),
        ]);

        Task::factory(30)->create([
            'creator_id' => $user->id,
            'creator_type' => get_class($user),
            'parent_task_id' => Task::find(rand(1, 10))->first()->id,
        ]);
        Bot::factory(2)->create([
            'user_id' => $user->id,
        ]);
    }
}
