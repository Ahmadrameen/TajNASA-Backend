<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'content'];

    public function photos()
    {
        return $this->morphMany(Photo::class, 'photoable');
    }

    public function members()
    {
        return $this->hasMany(ProjectMember::class);
    }

    // create scope if is admin by considering that in project_members table type is 1 and project_id is this project id and user_id is this user id
    public function scopeIsAdmin($query, $user_id)
    {
        return $query->whereHas('members', function ($query) use ($user_id) {
            $query->where('type', 1)->where('user_id', $user_id)->where('project_id', $this->id);
        });
    }

    public function tags()
    {
        return $this->hasManyThrough(
            Tag::class, // Target model (Tag)
            ProjectTag::class, // Intermediate model (ProjectTag)
            'project_id', // Foreign key on ProjectTag table
            'id', // Foreign key on Tag table
            'id', // Local key on Project table
            'tag_id' // Local key on ProjectTag table
        );
    }

}
