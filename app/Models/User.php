<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'first_name', 'last_name', 'middle_name', 'email', 'phone', 'avatar_path', 'avatar_mode', 'password', 'is_admin', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    public const ROLE_ADMIN = 'admin';
    public const ROLE_HEAD_OT = 'head_ot';
    public const ROLE_TESTER = 'tester';
    public const ROLE_EMPLOYEE = 'employee';

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function displayName(): string
    {
        $name = trim(implode(' ', array_filter([$this->last_name, $this->first_name, $this->middle_name])));

        return $name ?: $this->name;
    }

    /** @return array<string, string> */
    public static function roles(): array
    {
        return [
            self::ROLE_ADMIN => 'Администратор',
            self::ROLE_HEAD_OT => 'Начальник ОТ',
            self::ROLE_TESTER => 'Тестировщик',
            self::ROLE_EMPLOYEE => 'Сотрудник',
        ];
    }
}
