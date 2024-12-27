<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = collect([
            ['name' => 'My Project'],
            ['name' => 'Company Project'],
            ['name' => 'Other Project'],
        ]);

        $projects->transform(function ($project) {
            return [
                'name' => $project['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        Project::insert($projects->toArray());
    }
}
