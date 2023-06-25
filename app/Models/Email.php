<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    public $timestamps = false;

    public function contact() {
        return $this->belongsTo(Contact::class);
    }
}
