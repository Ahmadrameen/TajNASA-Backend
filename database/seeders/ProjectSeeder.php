<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run()
    {
        Project::create([
            'name' => 'Project 1',
            'description' => 'This is project 1',
            'content' => 'This is project 1 content'
        ]);

        Project::create([
            'name' => 'Project 2',
            'description' => 'This is project 2',
            'content' => 'This is project 2 content'
        ]);

        Project::create([
            'name' => 'Project 3',
            'description' => 'This is project 3',
            'content' => 'This is project 3 content'
        ]);
    }
}
