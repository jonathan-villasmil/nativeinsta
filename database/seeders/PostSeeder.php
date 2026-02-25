<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        $captions = [
            'Un día increíble en la playa 🌊 #verano #sol',
            'Nada como un buen café por la mañana ☕ #coffee #morning',
            'Explorando nuevos rincones de la ciudad 🌆 #urban #photography',
            'Tarde perfecta con amigos 🎉 #friends #goodvibes',
            'El atardecer de hoy fue espectacular 🌅 #sunset #nature',
            'Probando la nueva receta de pasta 🍝 #food #homecooking',
            'Entrenamiento completado 💪 #gym #fitness #motivation',
            'Leyendo un buen libro en el parque 📚 #reading #relax',
            'Concierto increíble esta noche 🎸 #music #livemusic',
            'Viaje de fin de semana ✈️ #travel #adventure #wanderlust',
        ];

        $comments = [
            '¡Qué bonito! 😍',
            'Me encanta esta foto ❤️',
            '¡Increíble! 🔥',
            'Qué envidia 😄',
            'Wow, precioso!',
            '¡Espectacular vista!',
            'Cuánto talento tienes 👏',
            '¡Me alegra verte feliz!',
            'Esto es arte puro 🎨',
            '¡Sigues sorprendiéndome!',
        ];

        foreach ($users as $user) {
            // 3–6 posts per user
            $postCount = rand(3, 6);
            for ($i = 0; $i < $postCount; $i++) {
                // Use picsum.photos for random placeholder images
                $seed  = rand(1, 1000);
                $post  = Post::create([
                    'user_id'    => $user->id,
                    'image_path' => "https://picsum.photos/seed/{$seed}/600/600",
                    'caption'    => $captions[array_rand($captions)],
                    'created_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
                    'updated_at' => now()->subDays(rand(0, 30)),
                ]);

                // Random likes from other users
                $likers = $users->where('id', '!=', $user->id)->random(rand(0, min(5, $users->count() - 1)));
                foreach ($likers as $liker) {
                    $post->likes()->create(['user_id' => $liker->id]);
                }

                // 0–3 comments
                $commentCount = rand(0, 3);
                for ($j = 0; $j < $commentCount; $j++) {
                    $commenter = $users->random();
                    Comment::create([
                        'post_id' => $post->id,
                        'user_id' => $commenter->id,
                        'body'    => $comments[array_rand($comments)],
                    ]);
                }
            }
        }
    }
}
