<?php

namespace App\Providers;

use App\Helpers\Hashtag;
use App\Helpers\Mention;
use App\Helpers\TextParser;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // @mention($text) — kept for backwards compatibility
        Blade::directive('mention', function (string $expression) {
            return "<?php echo App\\Helpers\\Mention::parse({$expression}); ?>";
        });

        // @hashtag($text) — kept for backwards compatibility
        Blade::directive('hashtag', function (string $expression) {
            return "<?php echo App\\Helpers\\Hashtag::parse({$expression}); ?>";
        });

        // @rendertext($text) — unified: escapes once, then applies @mention + #hashtag
        Blade::directive('rendertext', function (string $expression) {
            return "<?php echo App\\Helpers\\TextParser::render({$expression}); ?>";
        });
    }
}
