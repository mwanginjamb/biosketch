<?php

declare(strict_types=1);

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * An API bearer token. Only the SHA-256 hash is persisted.
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $name
 * @property string $token_hash
 * @property int|null $expires_at
 * @property int|null $last_used_at
 * @property int $created_at
 *
 * @property User $user
 */
class UserApiToken extends ActiveRecord
{
    /** Default token lifetime: 30 days. Override with params['api.tokenTtl'] (seconds, 0 = never expires). */
    public const DEFAULT_TTL = 30 * 24 * 3600;

    /** Don't write last_used_at more often than this (seconds). */
    private const TOUCH_INTERVAL = 300;

    public static function tableName(): string
    {
        return '{{%user_api_token}}';
    }

    public static function hash(string $token): string
    {
        return hash('sha256', $token);
    }

    /**
     * Issues a new token for $user.
     *
     * @param int|null $ttl lifetime in seconds; null = params['api.tokenTtl'] / DEFAULT_TTL, 0 = never expires
     * @return array{0: self, 1: string} the stored record and the plain token (show it once).
     */
    public static function issue(User $user, ?string $name = null, ?int $ttl = null): array
    {
        $plain = Yii::$app->security->generateRandomString(48);
        $ttl ??= (int) (Yii::$app->params['api.tokenTtl'] ?? self::DEFAULT_TTL);

        $token = new self();
        $token->user_id = $user->id;
        $token->name = $name !== null ? mb_substr($name, 0, 100) : null;
        $token->token_hash = self::hash($plain);
        $token->created_at = time();
        $token->expires_at = $ttl > 0 ? time() + $ttl : null;
        $token->save(false);

        return [$token, $plain];
    }

    /** Finds an unexpired token record matching the plain token. */
    public static function findActive(string $plain): ?self
    {
        if ($plain === '') {
            return null;
        }

        return static::find()
            ->where(['token_hash' => self::hash($plain)])
            ->andWhere(['or', ['expires_at' => null], ['>', 'expires_at', time()]])
            ->one();
    }

    /** Records usage, at most once per TOUCH_INTERVAL. */
    public function touchLastUsed(): void
    {
        $now = time();
        if ($this->last_used_at === null || $now - (int) $this->last_used_at >= self::TOUCH_INTERVAL) {
            $this->updateAttributes(['last_used_at' => $now]);
        }
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
