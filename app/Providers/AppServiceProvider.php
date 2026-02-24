<?php

namespace App\Providers;

use App\Helpers\Mention;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Custom Blade directive: @mention($text)
        // Usage in views: @mention($post->caption)
        Blade::directive('mention', function (string $expression) {
            return "<?php echo App\\Helpers\\Mention::parse({$expression}); ?>";
        });
    }
}
