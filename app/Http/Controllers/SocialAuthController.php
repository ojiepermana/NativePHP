<?php

namespace App\Http\Controllers;

use App\Models\User;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return $this->redirectToProvider('google');
    }

    public function redirectToMicrosoft()
    {
        return $this->redirectToProvider('microsoft');
    }

    public function redirectToApple()
    {
        return $this->redirectToProvider('apple');
    }

    private function redirectToProvider(string $provider)
    {
        // Placeholder untuk integrasi OAuth
        // Implementasi nyata akan menggunakan Socialite atau Laravel Passport
        // Untuk saat ini, redirect ke login dengan pesan
        return redirect()
            ->route('login')
            ->with('error', "Login dengan {$provider} belum dikonfigurasi.");
    }

    public function handleGoogleCallback()
    {
        return $this->handleProviderCallback('google');
    }

    public function handleMicrosoftCallback()
    {
        return $this->handleProviderCallback('microsoft');
    }

    public function handleAppleCallback()
    {
        return $this->handleProviderCallback('apple');
    }

    private function handleProviderCallback(string $provider)
    {
        // Placeholder untuk integrasi OAuth callback
        // Implementasi nyata akan memverifikasi user dari provider
        return redirect()
            ->route('login')
            ->with('error', "Callback {$provider} belum dikonfigurasi.");
    }
}
