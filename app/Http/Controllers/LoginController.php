<?php

namespace App\Http\Controllers;

use App\Mail\TempPassword;
use App\Models\Session;
use App\Models\Word;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    public function login()
    {
        if (Auth::user()) {
            return view('auth.already-logged-in');
        }

        return view('auth.login');
    }

    public function requestPassword(Request $request)
    {
        if (Auth::user()) {
            return view('auth.already-logged-in');
        }

        $credentials = $request->validate([
            'email' => ['required', 'email']
        ]);

        $user = Auth::getProvider()->retrieveByCredentials($credentials);
        if (!$user) {
            return view('auth.password', ['email' => $credentials['email']]);
        }

        if ($request->session()->get('password_count', 0) == 0) {
            $this->setAndSendPassword($user, $credentials['email']);
            return view('auth.password', [
                'email' => $credentials['email']]);
        }

        $password_expiry = Carbon::make($request->session()->get('password_sent'));
        $password_expiry = $password_expiry->addSeconds(256);
        if ($password_expiry->lessThan(Carbon::now())) {
            $this->setAndSendPassword($user, $credentials['email']);
        }

        return view('auth.password', [
            'email' => $credentials['email']]);
    }

    protected function setAndSendPassword($user, $email) {
        $words = Word::query()->inRandomOrder()->limit(3)->get();
        $password = $words[0]->word . '-' . $words[1]->word . '-' . $words[2]->word;
        $user->setAuthPassword($password);

        return Mail::to(new Address($email, $user->full_name))->send(new TempPassword($password));
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended();
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
}
