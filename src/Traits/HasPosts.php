<?php

namespace Tajul\Saajan\Traits;

use Tajul\Saajan\Models\Post;

trait HasPosts
{
  public function posts()
  {
    return $this->morphMany(Post::class, 'author');
  }
}