<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Forum extends Model
{
    protected $fillable = ['name', 'project_id'];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function scopeWhereUserIsMember($query, $user_id)
    {
        return $query->whereHas('project.members', function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        });
    }
}
