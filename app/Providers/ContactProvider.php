<?php

namespace App\Providers;

use App\Models\Contact;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class ContactProvider extends ServiceProvider implements UserProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    public function retrieveById($identifier) {
        return Contact::find($identifier);
    }

    public function retrieveByToken($identifier, $token) {
        return Contact::where('id', $identifier)::where('remember_me', $token)->first();
    }

    public function updateRememberToken(Contact|Authenticatable $user, $token) {
        $user->remember_me = $token;
        $user->save();
    }

    public function retrieveByCredentials(array $credentials) {
        $email_address = $credentials["email"];
    }

    public function validateCredentials(Authenticatable $user, array $credentials) {
        return false;
    }
}
