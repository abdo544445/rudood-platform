<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $workspace = \App\Models\Workspace::create([
            'company_name' => 'Test Company LLC',
            'plan_id' => 'premium',
            'status' => 'active'
        ]);

        \App\Models\Bot::create([
            'workspace_id' => $workspace->id,
            'name' => 'Customer Support Bot',
            'system_prompt' => 'You are a helpful assistant.',
            'model_type' => 'gpt-4',
            'is_active' => true
        ]);
    }
}
