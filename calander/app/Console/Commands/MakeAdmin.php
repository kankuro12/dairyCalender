<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class MakeAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin {name : Name of the admin} {email : Email of the admin} {password : Password of the admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $name = $this->argument('name');
    $password = $this->argument('password');

    if(Admin::where('email', $email)->exists()) {
        $this->error("Admin with email {$email} already exists.");
        return Command::FAILURE;
    }
    Admin::create([
        'name' => $name,
        'email' => $email,
        'password' => hash::make($password),
    ]);
    $this->info("Admin user {$name} created successfully.");
    return Command::SUCCESS;    

}
}
