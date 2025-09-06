<?php

namespace App;

use Illuminate\Support\Str;

trait HasUniqueSlug
{
    public static function generateUniqueSlug($value, $excludeId = null)
    {
        $slug = Str::slug($value);
        $originalSlug = $slug;
        $count = 1;

        $query = static::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = $originalSlug . '-' . $count++;
            $query = static::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }

        return $slug;
    }
}
