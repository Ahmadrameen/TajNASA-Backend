<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectTag extends Model
{
    protected $fillable = ['project_id', 'tag_id'];

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_tags', 'tag_id', 'project_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'project_tags', 'project_id', 'tag_id');
    }
}
