<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\ChangeTrackingService;

class ForumPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',    
        'content',   
        'author_id',
        'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($post) {
            app(ChangeTrackingService::class)->logChange(
                "Post {$post->id} created by User {$post->author_id}: {$post->title}, Content: {$post->content}"
            );
        });

        static::updated(function ($post) {
            app(ChangeTrackingService::class)->logChange(
                "Post {$post->id} updated by User {$post->author_id}: {$post->title}, Content: {$post->content}"
            );
        });

        static::deleted(function ($post) {
            app(ChangeTrackingService::class)->logChange(
                "Post {$post->id} deleted by User {$post->author_id}: {$post->title}, Content: {$post->content}"
            );
        });
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id')->select('id', 'name');
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['author_name'] = $this->author ? $this->author->name : null;
        return $array;
    }
}