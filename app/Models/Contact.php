<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Authenticatable
{
    use HasFactory, HasTimestamps, HasUlids;

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->id;
    }

    public function getAuthPassword()
    {
        $session = Session::getCurrrentSession();
        return $session->password_hash;
    }

    public function setAuthPassword(string $password): void
    {
        $session = Session::getCurrrentSession();
        $session->password_hash = $password;
        $session->save();
    }

    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
        $this->save();
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    public function emails() {
        return $this->hasMany(Email::class)->orderByDesc('priority');
    }

    public function sessions() {
        return $this->hasMany(Session::class);
    }
}
