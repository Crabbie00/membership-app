<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    protected $fillable = ['type','file_path','original_name','file_size','mime_type'];

    public function documentable(): Relations\MorphTo {
        return $this->morphTo();
    }

    public function getUrlAttribute(): string {
        return asset('storage/'.$this->file_path);
    }

    protected static function booted(): void
    {
        static::deleting(function (Document $doc) {
            if ($doc->file_path) {
                Storage::disk('public')->delete($doc->file_path);
            }
        });
    }
}
