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
}
