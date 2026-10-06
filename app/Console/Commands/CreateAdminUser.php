<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create {--first-name=} {--last-name=} {--middle-name=} {--email=}';

    protected $description = 'Create or promote an administrator account';

    public function handle(): int
    {
        $firstName = $this->option('first-name') ?: $this->ask('First name');
        $lastName = $this->option('last-name') ?: $this->ask('Last name');
        $middleName = $this->option('middle-name') ?: $this->ask('Middle name (optional)');
        $email = $this->option('email') ?: $this->ask('Email');
        $password = $this->secret('Password (at least 8 characters)');

        if (strlen((string) $password) < 8) {
            $this->error('Password must contain at least 8 characters.');

            return self::FAILURE;
        }

        User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => trim("{$lastName} {$firstName} {$middleName}"),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'middle_name' => $middleName,
                'password' => $password,
                'role' => User::ROLE_ADMIN,
                'is_admin' => true,
            ],
        );

        $this->info("Administrator {$email} is ready.");

        return self::SUCCESS;
    }
}
