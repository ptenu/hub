<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    public $timestamps = false;
    protected $dates = ['password_sent'];

    public static function getCurrrentSession() {
        $session_id = \Illuminate\Support\Facades\Session::getId();
        if ($session_id == null) {
            return null;
        }

        return Session::find($session_id);
    }

    public function contact() {
        return $this->belongsTo(Contact::class);
    }
}
