<?php

namespace KirschbaumDevelopment\NovaComments\Models;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\Models\Media ;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media as MediaCollection;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model  implements HasMedia
{
    use InteractsWithMedia ;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nova_comments';

    /**
     * The "booting" method of the model.
     */
    public static function boot()
    {
        parent::boot();

        static::creating(
            function ($comment) {
                if (auth()->check()) {
                    $comment->commenter_id = auth()->id();
                }
            }
        );
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('comments-image');
        $this->addMediaCollection('comments-attach');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function commenter()
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'commenter_id');
    }

}
