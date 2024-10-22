<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title_en',
        'title_ar',
        'title_de',
        'slug',
        'author_id',
        'summary_en',
        'summary_ar',
        'summary_de',
        'status',
        'image',
        'keywords',
    ];



    /**
     * Boot method to handle model events.
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically generate slug from title_en when creating a new blog post
        static::creating(function ($blog) {
            if (empty($blog->slug)) {
                $blog->slug = Str::slug($blog->title_en);
            }
        });

        // Optionally, update slug when title_en is updated
        static::updating(function ($blog) {
            if ($blog->isDirty('title_en')) {
                $blog->slug = Str::slug($blog->title_en);
            }
        });
    }

    public function contentItems()
    {
        return $this->hasMany(ContentItem::class, 'post_id', 'id');
    }
}
