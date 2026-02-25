<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Demo account (always available) ─────────────────────
        $demo = User::firstOrCreate(
            ['email' => 'demo@nativeinsta.test'],
            [
                'name'              => 'Demo User',
                'username'          => 'demo',
                'password'          => Hash::make('password'),
                'bio'               => '¡Hola! Soy el usuario de prueba de NativeInsta 👋',
                'email_verified_at' => now(),
            ]
        );

        // ── 10 random users ──────────────────────────────────────
        User::factory(10)->create();

        // ── Follows ──────────────────────────────────────────────
        $this->call(FollowSeeder::class);

        // ── Posts, likes, comments ───────────────────────────────
        $this->call(PostSeeder::class);
    }
}
