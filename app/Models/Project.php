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
}
