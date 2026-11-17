<?php

declare(strict_types=1);

namespace frontend\tests\Support\Helper;

use Codeception\Module;

final class Functional extends Module
{
    public function seeValidationError(string $message): void
    {
        $this->getModule('Yii2')->see($message, '.invalid-feedback');
    }

    public function dontSeeValidationError(string $message): void
    {
        $this->getModule('Yii2')->dontSee($message, '.invalid-feedback');
    }

    /**
     * Header of the last response (the Yii2 module has no header assertions of its own).
     */
    public function grabResponseHeader(string $name): ?string
    {
        /** @var \Codeception\Module\Yii2 $yii2 */
        $yii2 = $this->getModule('Yii2');

        return $yii2->client->getInternalResponse()->getHeader($name);
    }
}
