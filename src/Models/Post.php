<?php

namespace Tajul\Saajan\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tajul\Saajan\Database\Factories\PostFactory;

class Post extends Model
{
    use HasFactory;

    // Disable Laravel's mass assignment protection
    protected $guarded = [];

    protected static function newFactory()
    {
        return PostFactory::new();
    }

    public function author()
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }
}
