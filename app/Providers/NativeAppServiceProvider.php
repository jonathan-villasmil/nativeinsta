<?php

namespace App\Providers;

use Native\Desktop\Facades\Window;
use Native\Desktop\Contracts\ProvidesPhpIni;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);

        Window::open()
            ->width(1100)
            ->height(750)
            ->minWidth(800)
            ->minHeight(600)
            ->title('NativeInsta');
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
        ];
    }
}
