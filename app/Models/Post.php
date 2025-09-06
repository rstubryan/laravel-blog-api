<?php

namespace App\Models;

use App\HasUniqueSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory, HasUniqueSlug;

    protected $fillable = ['title', 'content', 'author', 'slug', 'category_id'];
}
