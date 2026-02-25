<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = ['name', 'username', 'password', 'role', 'ue1'];
    protected $hidden = ['password', 'remember_token'];

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }
    public function canManageUsers(): bool
    {
        return $this->isSuperAdmin();
    }
    public function canAccessAllUnits(): bool
    {
        return $this->isSuperAdmin() || $this->isAdmin();
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'super_admin' => 'Super Admin',
            'admin' => 'Admin',
            'operator' => 'Operator',
            default => $this->role,
        };
    }
    public function getUe1NamaAttribute(): string
    {
        return ($this->ue1 && isset(Pegawai::UE1_LIST[$this->ue1])) ? Pegawai::UE1_LIST[$this->ue1] : '-';
    }
    public function getUe1ShortAttribute(): string
    {
        return ($this->ue1 && isset(Pegawai::UE1_SHORT[$this->ue1])) ? Pegawai::UE1_SHORT[$this->ue1] : '-';
    }
}
