<?php

namespace App\Providers;

use App\Extensions\ContactSessionHandler;
use App\Models\Contact;
use App\Models\Email;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\UserProvider as UserContract;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

class ContactProvider extends ServiceProvider implements UserContract
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
        Session::extend('contact', function(Application $app) {
            return new ContactSessionHandler(DB::connection(), 'sessions', env("SESSION_LIFETIME"));
        });
    }

    public function retrieveById($identifier) {
        return Contact::find($identifier);
    }

    public function retrieveByToken($identifier, $token) {
        return Contact::where('id', $identifier)::where('remember_me', $token)->first();
    }

    public function updateRememberToken(Authenticatable $user, $token): void
    {
        $user->remember_me = $token;
        $user->save();
    }

    public function retrieveByCredentials(array $credentials) {
        $email_address = $credentials["email"];
        $email = Email::where('address', $email_address)->first();
        if ($email == null) {
            return null;
        }
        return $email->contact;
    }

    public function validateCredentials(Authenticatable $user, array $credentials) {
        if (is_null($plain = $credentials['password'])) {
            return false;
        }

        $result = Hash::check($plain, $user->getAuthPassword());
        if (!$result) {
            return false;
        }

        $email = Email::query()->where('address', $credentials['email'])->first();
        if (!$email) {
            return false;
        }

        session(['verified_at' => Carbon::now()]);
        $email->verified_at = Carbon::now();
        $email->save();

        return true;
    }

}
