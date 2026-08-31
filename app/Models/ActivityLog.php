<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'subject_type',
        'subject_id',
        'metadata',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public const ACTION_LOGIN = 'login';
    public const ACTION_LOGOUT = 'logout';
    public const ACTION_ACCOUNT_CREATED = 'account_created';
    public const ACTION_PROFILE_UPDATE = 'profile_update';
    public const ACTION_PASSWORD_CHANGE = 'password_change';
    public const ACTION_TECNOLOGIA_CREATED = 'tecnologia_created';
    public const ACTION_TECNOLOGIA_UPDATED = 'tecnologia_updated';
    public const ACTION_TECNOLOGIA_PUBLISHED = 'tecnologia_published';
    public const ACTION_TECNOLOGIA_DELETED = 'tecnologia_deleted';
    public const ACTION_ADMIN_TOGGLE = 'admin_toggle';
    public const ACTION_USER_DELETED = 'user_deleted';

    public static function actionLabels(): array
    {
        return [
            self::ACTION_LOGIN => 'Login',
            self::ACTION_LOGOUT => 'Logout',
            self::ACTION_ACCOUNT_CREATED => 'Conta criada',
            self::ACTION_PROFILE_UPDATE => 'Perfil atualizado',
            self::ACTION_PASSWORD_CHANGE => 'Senha alterada',
            self::ACTION_TECNOLOGIA_CREATED => 'Tecnologia criada',
            self::ACTION_TECNOLOGIA_UPDATED => 'Tecnologia editada',
            self::ACTION_TECNOLOGIA_PUBLISHED => 'Tecnologia publicada',
            self::ACTION_TECNOLOGIA_DELETED => 'Tecnologia excluída',
            self::ACTION_ADMIN_TOGGLE => 'Permissão alterada',
            self::ACTION_USER_DELETED => 'Usuário excluído',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function getActionLabelAttribute(): string
    {
        return self::actionLabels()[$this->action] ?? $this->action;
    }
}
