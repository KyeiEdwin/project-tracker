<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $demoUser = User::query()->updateOrCreate(
            ['email' => 'demo@example.com'],
            ['name' => 'Demo User', 'password' => 'password']
        );

        $demos = [
            [
                'name' => 'Website Redesign',
                'description' => 'Complete redesign of corporate website',
                'team' => 'Marketing Team',
                'status' => 'in-progress',
                'progress' => 75,
                'priority' => 'high',
                'start_date' => '2024-10-01',
                'due_date' => '2024-12-15',
                'budget' => 50000,
                'spent' => 35000,
                'client' => 'Acme Corp',
                'project_type' => 'agile',
                'owner_id' => $demoUser->id,
            ],
            [
                'name' => 'Mobile App Development',
                'description' => 'Native iOS and Android app development',
                'team' => 'Development Team',
                'status' => 'on-hold',
                'progress' => 45,
                'priority' => 'medium',
                'start_date' => '2024-08-01',
                'due_date' => '2024-12-20',
                'budget' => 120000,
                'spent' => 54000,
                'client' => 'Northwind',
                'project_type' => 'hybrid',
                'owner_id' => $demoUser->id,
            ],
            [
                'name' => 'CRM Integration',
                'description' => 'Integrate Salesforce with internal systems',
                'team' => 'IT Team',
                'status' => 'completed',
                'progress' => 100,
                'priority' => 'low',
                'start_date' => '2024-06-01',
                'due_date' => '2024-11-30',
                'budget' => 30000,
                'spent' => 28500,
                'client' => 'Contoso',
                'project_type' => 'predictive',
                'owner_id' => $demoUser->id,
            ],
            [
                'name' => 'Data Migration',
                'description' => 'Migrate legacy data to new cloud platform',
                'team' => 'Database Team',
                'status' => 'in-progress',
                'progress' => 60,
                'priority' => 'high',
                'start_date' => '2024-09-01',
                'due_date' => '2024-12-10',
                'budget' => 75000,
                'spent' => 45000,
                'client' => 'Internal',
                'project_type' => 'predictive',
                'owner_id' => $demoUser->id,
            ],
            [
                'name' => 'Security Audit',
                'description' => 'Annual security assessment and compliance review',
                'team' => 'Security Team',
                'status' => 'planning',
                'progress' => 10,
                'priority' => 'high',
                'start_date' => '2024-11-01',
                'due_date' => '2024-12-25',
                'budget' => 25000,
                'spent' => 2500,
                'client' => 'Compliance Office',
                'project_type' => 'agile',
                'owner_id' => $demoUser->id,
            ],
        ];

        foreach ($demos as $demo) {
            Project::query()->updateOrCreate(
                ['name' => $demo['name']],
                $demo
            );
        }
    }
}
