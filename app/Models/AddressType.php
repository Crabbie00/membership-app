<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;

class AddressType extends Model
{
    protected $fillable = ['name','status'];

    public function addresses(): Relations\HasMany {
        return $this->hasMany(Address::class);
    }
}
