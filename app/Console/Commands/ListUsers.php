<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ListUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all users in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all(['name', 'email', 'role', 'created_at']);

        if ($users->isEmpty()) {
            $this->info('No users found in the database.');
            return;
        }

        $this->info('Users in database:');
        $this->table(
            ['Name', 'Email', 'Role', 'Created At'],
            $users->map(function ($user) {
                return [
                    $user->name,
                    $user->email,
                    $user->role,
                    $user->created_at->format('Y-m-d H:i:s'),
                ];
            })
        );
    }
} 
