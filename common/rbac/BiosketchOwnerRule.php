<?php

declare(strict_types=1);

namespace common\rbac;

use yii\rbac\Item;
use yii\rbac\Rule;

/**
 * Passes when the biosketch (Researcher) passed as $params['researcher'] belongs to the user.
 *
 *   Yii::$app->user->can('updateBiosketch', ['researcher' => $model])
 */
class BiosketchOwnerRule extends Rule
{
    public $name = 'isBiosketchOwner';

    /**
     * @param string|int $user the user ID
     * @param Item $item
     * @param array $params must contain 'researcher' with a user_id attribute
     */
    public function execute($user, $item, $params): bool
    {
        $researcher = $params['researcher'] ?? null;

        return $researcher !== null
            && $user !== null
            && (int) $researcher->user_id === (int) $user;
    }
}
