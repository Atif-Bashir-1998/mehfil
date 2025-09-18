<?php

namespace Database\Seeders;

use App\Models\User;
use App\Utils\RoleHelper;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::create(['name' => RoleHelper::DEFAULT_ROLES['ADMIN']]);
        Role::create(['name' => RoleHelper::DEFAULT_ROLES['DEVELOPER']]);
        Role::create(['name' => RoleHelper::DEFAULT_ROLES['MODERATOR']]);

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]);
        $admin->assignRole(RoleHelper::DEFAULT_ROLES['ADMIN']);

        $developer = User::factory()->create([
            'name' => 'Developer',
            'email' => 'developer@example.com',
        ]);
        $developer->assignRole(RoleHelper::DEFAULT_ROLES['DEVELOPER']);

        $moderator = User::factory()->create([
            'name' => 'Moderator',
            'email' => 'moderator@example.com',
        ]);
        $moderator->assignRole(RoleHelper::DEFAULT_ROLES['MODERATOR']);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory(3)->create();

        $this->call([
            PostSeeder::class
        ]);
    }
}
