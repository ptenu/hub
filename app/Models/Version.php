<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Version extends Model
{
    use SoftDeletes, HasTimestamps, HasUlids;

    public $touches = ['document'];

    public function document(): BelongsTo {
        return $this->belongsTo(Document::class);
    }

    public function contact(): BelongsTo {
        return $this->belongsTo(Contact::class);
    }
}
