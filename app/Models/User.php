<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Papéis permitidos (ERS/RBAC)
    public const ROLE_ADMIN        = 'ADMIN';
    public const ROLE_FUNCIONARIO  = 'FUNCIONARIO';
    public const ROLE_PROPRIETARIO = 'PROPRIETARIO';
    public const ROLE_CLIENTE      = 'CLIENTE';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'papel',   // ✅ oficial
        'role',    // compat (podes remover no futuro)
        'ativo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'ativo' => 'boolean',
        ];
    }

    /**
     * Papel oficial do utilizador.
     * - usa 'papel' como fonte de verdade
     * - cai para 'role' se vier legado
     */
    public function getPapelAttribute($value): ?string
    {
        return $value ?? $this->attributes['role'] ?? null;
    }

    /**
     * (Opcional) Se alguém setar papel, sincroniza role também.
     * Ajuda a não haver conflitos enquanto o campo role existir.
     */
    public function setPapelAttribute($value): void
    {
        $this->attributes['papel'] = $value;
        $this->attributes['role']  = $value; // mantém sincronizado
    }

    // -------- Helpers de perfil/estado --------
    public function isAdmin(): bool { return $this->papel === self::ROLE_ADMIN; }
    public function isFuncionario(): bool { return $this->papel === self::ROLE_FUNCIONARIO; }
    public function isProprietario(): bool { return $this->papel === self::ROLE_PROPRIETARIO; }
    public function isCliente(): bool { return $this->papel === self::ROLE_CLIENTE; }
    public function isAtivo(): bool { return (bool) $this->ativo; }

    // -------- Relações --------
    public function owner()
    {
        return $this->hasOne(\App\Models\Owner::class);
    }

    public function reservations()
    {
        return $this->hasMany(\App\Models\Reservation::class);
    }

    public function audits()
    {
        return $this->hasMany(\App\Models\Audit::class);
    }
}
