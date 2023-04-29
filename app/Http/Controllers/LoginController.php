<?php

namespace App\Http\Controllers;

use App\Models\Word;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // TODO: return login view
    }
    public function requestPassword(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email']
        ]);

        $user = Auth::getProvider()->retrieveByCredentials($credentials);
        if ($user == null) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        $words = Word::query()->inRandomOrder()->limit(3)->get();
        $password = $words[0]->word . '-' . $words[1]->word . '-' . $words[2]->word;
        $user->setAuthPassword($password);
        // TODO: Send an email containing a temporary password

        // TODO: Return password entry view
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
