<?php

declare(strict_types=1);

namespace common\components;

use Yii;

/**
 * Minimal cache-backed request throttle, e.g. per client IP.
 *
 *   if (!Throttle::hit('api-token', $ip, 10, 600)) { // 429 }
 *
 * The counter expires $window seconds after the last allowed hit.
 */
final class Throttle
{
    public static function hit(string $bucket, string $key, int $limit, int $window): bool
    {
        $cache = Yii::$app->cache;
        if ($cache === null) {
            return true;
        }

        $cacheKey = ['throttle', $bucket, $key];
        $hits = (int) $cache->get($cacheKey);
        if ($hits >= $limit) {
            return false;
        }
        $cache->set($cacheKey, $hits + 1, $window);

        return true;
    }
}
