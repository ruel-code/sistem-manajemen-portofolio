<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Workspace;
use App\Models\Project;
use App\Models\Task;
use App\Models\ChatChannel;
use App\Models\TaskLabel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // =====================
        // Create Roles
        // =====================
        $roles = ['super_admin', 'project_manager', 'team_member', 'client'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // =====================
        // Create Users
        // =====================
        $admin = User::firstOrCreate(
            ['email' => 'admin@nexacrm.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'job_title' => 'System Administrator',
                'is_active' => true,
            ]
        );
        $admin->assignRole('super_admin');

        $pm = User::firstOrCreate(
            ['email' => 'manager@nexacrm.com'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'job_title' => 'Project Manager',
                'is_active' => true,
            ]
        );
        $pm->assignRole('project_manager');

        $dev1 = User::firstOrCreate(
            ['email' => 'dev1@nexacrm.com'],
            [
                'name' => 'Andi Wijaya',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'job_title' => 'Frontend Developer',
                'is_active' => true,
            ]
        );
        $dev1->assignRole('team_member');

        $dev2 = User::firstOrCreate(
            ['email' => 'dev2@nexacrm.com'],
            [
                'name' => 'Siti Rahayu',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'job_title' => 'Backend Developer',
                'is_active' => true,
            ]
        );
        $dev2->assignRole('team_member');

        $client = User::firstOrCreate(
            ['email' => 'client@nexacrm.com'],
            [
                'name' => 'PT Maju Jaya',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'job_title' => 'Client',
                'is_active' => true,
            ]
        );
        $client->assignRole('client');

        // =====================
        // Create Workspace
        // =====================
        $workspace = Workspace::firstOrCreate(
            ['slug' => 'nexacrm-demo'],
            [
                'name' => 'NexaCRM Demo',
                'description' => 'Demo workspace untuk NexaCRM',
                'owner_id' => $admin->id,
                'color' => '#6366f1',
                'plan' => 'pro',
            ]
        );

        // Add members
        $members = [
            [$admin->id, 'owner'],
            [$pm->id, 'admin'],
            [$dev1->id, 'member'],
            [$dev2->id, 'member'],
            [$client->id, 'client'],
        ];

        foreach ($members as [$userId, $role]) {
            if (!$workspace->members()->where('user_id', $userId)->exists()) {
                $workspace->members()->attach($userId, ['role' => $role, 'joined_at' => now()]);
            }
        }

        // =====================
        // Create Task Labels
        // =====================
        $labels = [
            ['name' => 'Bug', 'color' => '#ef4444'],
            ['name' => 'Feature', 'color' => '#6366f1'],
            ['name' => 'UI/UX', 'color' => '#8b5cf6'],
            ['name' => 'Backend', 'color' => '#f97316'],
            ['name' => 'Frontend', 'color' => '#06b6d4'],
            ['name' => 'Testing', 'color' => '#10b981'],
        ];

        foreach ($labels as $label) {
            TaskLabel::firstOrCreate(['workspace_id' => $workspace->id, 'name' => $label['name']], $label + ['workspace_id' => $workspace->id]);
        }

        // =====================
        // Create Projects
        // =====================
        $project1 = Project::firstOrCreate(
            ['slug' => 'website-company-profile'],
            [
                'workspace_id' => $workspace->id,
                'name' => 'Website Company Profile',
                'description' => 'Pembuatan website company profile modern untuk klien PT Maju Jaya',
                'status' => 'active',
                'priority' => 'high',
                'progress' => 65,
                'start_date' => now()->subDays(20),
                'due_date' => now()->addDays(15),
                'budget' => 15000000,
                'manager_id' => $pm->id,
                'client_id' => $client->id,
                'created_by' => $admin->id,
                'color' => '#6366f1',
            ]
        );
        $project1->members()->syncWithoutDetaching([
            $pm->id => ['role' => 'manager'],
            $dev1->id => ['role' => 'member'],
            $dev2->id => ['role' => 'member'],
        ]);

        $project2 = Project::firstOrCreate(
            ['slug' => 'mobile-app-ecommerce'],
            [
                'workspace_id' => $workspace->id,
                'name' => 'Mobile App E-Commerce',
                'description' => 'Pengembangan aplikasi mobile e-commerce dengan React Native',
                'status' => 'planning',
                'priority' => 'urgent',
                'progress' => 20,
                'start_date' => now()->subDays(5),
                'due_date' => now()->addDays(60),
                'budget' => 50000000,
                'manager_id' => $pm->id,
                'created_by' => $admin->id,
                'color' => '#8b5cf6',
            ]
        );

        $project3 = Project::firstOrCreate(
            ['slug' => 'sistem-erp-internal'],
            [
                'workspace_id' => $workspace->id,
                'name' => 'Sistem ERP Internal',
                'description' => 'Pengembangan sistem ERP untuk manajemen internal perusahaan',
                'status' => 'review',
                'priority' => 'medium',
                'progress' => 90,
                'start_date' => now()->subDays(60),
                'due_date' => now()->addDays(5),
                'budget' => 75000000,
                'manager_id' => $pm->id,
                'created_by' => $admin->id,
                'color' => '#06b6d4',
            ]
        );

        // =====================
        // Create Tasks for Project 1
        // =====================
        $tasksData = [
            ['title' => 'Desain wireframe homepage', 'status' => 'done', 'priority' => 'high', 'assigned_to' => $dev1->id, 'due_date' => now()->subDays(10)],
            ['title' => 'Implementasi header & navigation', 'status' => 'done', 'priority' => 'medium', 'assigned_to' => $dev1->id, 'due_date' => now()->subDays(5)],
            ['title' => 'Desain UI halaman About Us', 'status' => 'in_progress', 'priority' => 'medium', 'assigned_to' => $dev1->id, 'due_date' => now()->addDays(3)],
            ['title' => 'Setup backend API', 'status' => 'done', 'priority' => 'high', 'assigned_to' => $dev2->id, 'due_date' => now()->subDays(8)],
            ['title' => 'Integrasi form contact', 'status' => 'in_progress', 'priority' => 'high', 'assigned_to' => $dev2->id, 'due_date' => now()->addDays(2)],
            ['title' => 'SEO optimization', 'status' => 'todo', 'priority' => 'low', 'assigned_to' => $dev1->id, 'due_date' => now()->addDays(10)],
            ['title' => 'Testing & QA', 'status' => 'todo', 'priority' => 'urgent', 'assigned_to' => $dev2->id, 'due_date' => now()->addDays(12)],
            ['title' => 'Deployment ke server', 'status' => 'todo', 'priority' => 'high', 'assigned_to' => $pm->id, 'due_date' => now()->addDays(15)],
        ];

        foreach ($tasksData as $i => $taskData) {
            Task::firstOrCreate(
                ['project_id' => $project1->id, 'title' => $taskData['title']],
                array_merge($taskData, [
                    'project_id' => $project1->id,
                    'workspace_id' => $workspace->id,
                    'created_by' => $admin->id,
                    'order' => $i,
                    'completed_at' => $taskData['status'] === 'done' ? now()->subDays(rand(1, 5)) : null,
                ])
            );
        }

        // =====================
        // Create Chat Channel
        // =====================
        $general = ChatChannel::firstOrCreate(
            ['workspace_id' => $workspace->id, 'name' => 'general'],
            [
                'type' => 'group',
                'description' => 'Channel umum untuk semua tim',
                'created_by' => $admin->id,
            ]
        );

        foreach ([$admin->id, $pm->id, $dev1->id, $dev2->id] as $uid) {
            if (!$general->members()->where('user_id', $uid)->exists()) {
                $general->members()->attach($uid);
            }
        }

        // Sample messages
        if ($general->messages()->count() === 0) {
            $msgs = [
                [$admin->id, 'Selamat datang di NexaCRM! 🎉 Mari kita mulai kolaborasi yang produktif.'],
                [$pm->id, 'Siap! Project website company profile sudah berjalan dengan baik.'],
                [$dev1->id, 'Wireframe sudah selesai, siap untuk review 👍'],
                [$dev2->id, 'Backend API sudah live, silakan test endpoint-nya.'],
            ];
            foreach ($msgs as [$userId, $content]) {
                $general->messages()->create([
                    'user_id' => $userId,
                    'content' => $content,
                    'type' => 'text',
                ]);
            }
        }

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('');
        $this->command->info('👤 Login credentials:');
        $this->command->info('   Super Admin : admin@nexacrm.com / password');
        $this->command->info('   PM          : manager@nexacrm.com / password');
        $this->command->info('   Developer   : dev1@nexacrm.com / password');
        $this->command->info('   Client      : client@nexacrm.com / password');
    }
}
