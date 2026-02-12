<?php

namespace App\Providers;

use Native\Laravel\Contracts\ProvidesPhpIni;
use Native\Laravel\Events\App\OpenedFromURL;
use Native\Laravel\Facades\Window;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        Window::open()
            ->frameless()
            ->width(1400)
            ->height(1000)
            ->transparent(true);

        // Handle deep link URL
        OpenedFromURL::listen(function (OpenedFromURL $event) {
            $url = $event->url;

            // Parse the deep link URL
            // Format: idsapp://auth/verify?token=xxx
            if (str_starts_with($url, config('nativephp.deeplink_scheme').'://')) {
                $path = str_replace(config('nativephp.deeplink_scheme').'://', '', $url);

                // Navigate to the path in the app
                Window::current()->url(config('app.url').'/'.$path);
            }
        });
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [];
    }
}
