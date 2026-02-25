<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class FollowSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // Each user follows 3–7 random others
            $toFollow = $users
                ->where('id', '!=', $user->id)
                ->random(min(rand(3, 7), $users->count() - 1));

            foreach ($toFollow as $target) {
                // Avoid duplicate follows
                if (!$user->following()->where('following_id', $target->id)->exists()) {
                    $user->following()->attach($target->id);
                }
            }
        }
    }
}
