<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasUlids, HasTimestamps, SoftDeletes;

    public function versions(): HasMany {
        return $this->hasMany(Version::class);
    }

    public function editors(): HasManyThrough {
        return $this->hasManyThrough(Contact::class, Version::class);
    }

    public function currentVersion(): HasOne {
        return $this->hasOne(Version::class)->ofMany('created_at', 'max');
    }
}
