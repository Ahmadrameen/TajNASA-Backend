<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['content', 'user_id', 'forum_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function forum()
    {
        return $this->belongsTo(Forum::class);
    }

    public function scopeWhereUserIsMemberOfProject($query, $user_id)
    {
        return $query->whereHas('forum.project.members', function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        });
    }
}
