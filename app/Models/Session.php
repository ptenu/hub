<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
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
