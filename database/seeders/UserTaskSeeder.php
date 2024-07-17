<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Task;

class UserTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $users = User::factory(10)->create();

        Task::factory(50)->create([
            'user_id' => $users->random()->id,
        ]);
    }
}