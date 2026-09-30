<?php
namespace App\Console\Commands;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
class CreatePlatformAdmin extends Command {
    protected $signature = 'platform:admin {email?} {--name=Platform Administrator}';
    protected $description = 'Create or promote the initial platform administrator securely.';
    public function handle(): int {
        $email = $this->argument('email') ?: $this->ask('Email');
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) { $this->error('A valid email is required.'); return self::FAILURE; }
        $password = $this->secret('Password (minimum 12 characters)');
        if (! is_string($password) || strlen($password) < 12) { $this->error('Password must be at least 12 characters.'); return self::FAILURE; }
        $user = User::updateOrCreate(['email'=>$email],[
            'name'=>(string)$this->option('name'), 'password'=>Hash::make($password),
            'is_platform_admin'=>true, 'is_active'=>true, 'email_verified_at'=>now(),
        ]);
        $this->info("Platform administrator ready: {$user->email}");
        return self::SUCCESS;
    }
}
