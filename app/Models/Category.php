<?php

namespace App\Models;

use App\HasUniqueSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory, HasUniqueSlug;

    protected $fillable = ['name', 'description', 'slug'];
}
