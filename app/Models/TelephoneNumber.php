<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelephoneNumber extends Model
{
    public $timestamps = false;

    public function contact() {
        return $this->belongsTo(Contact::class);
    }

    public function normalisedNumber()
    {
        if (str_starts_with($this->number, "0")) {
            return '44' . substr($this->number, 1);
        }

        if (str_starts_with($this->number, "44")) {
            return $this->number;
        }

        return false;
    }
}
