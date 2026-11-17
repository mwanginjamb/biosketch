<?php

declare(strict_types=1);

namespace frontend\components;

use frontend\models\Researcher;
use RuntimeException;
use Yii;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * Stores researcher profile photos under @frontend/web/uploads/researchers/profile.
 *
 * Paths returned and accepted are web paths ("/uploads/researchers/profile/x.png").
 * Callers must validate the upload (Researcher::$attachment rules) before save().
 */
final class ProfilePhotoStorage
{
    private const WEB_DIR = '/uploads/researchers/profile';

    /** Saves a validated upload and returns its web path. */
    public static function save(Researcher $model, UploadedFile $file): string
    {
        $uploadDir = self::directory();
        if (!is_dir($uploadDir)) {
            FileHelper::createDirectory($uploadDir, 0775, true);
        }

        $filename = sprintf(
            'researcher_%d_%s.%s',
            $model->id,
            Yii::$app->security->generateRandomString(6),
            strtolower($file->extension),
        );

        if (!$file->saveAs($uploadDir . DIRECTORY_SEPARATOR . $filename)) {
            throw new RuntimeException('Unable to save researcher profile photo.');
        }

        return self::WEB_DIR . '/' . $filename;
    }

    /** Deletes a stored photo; ignores anything that does not resolve inside the uploads directory. */
    public static function delete(?string $webPath): void
    {
        if (!$webPath) {
            return;
        }

        $uploadDir = realpath(self::directory());
        $absolutePath = realpath(Yii::getAlias('@frontend/web') . $webPath);

        if (
            $uploadDir === false
            || $absolutePath === false
            || !str_starts_with($absolutePath, $uploadDir . DIRECTORY_SEPARATOR)
            || !is_file($absolutePath)
        ) {
            return;
        }

        unlink($absolutePath);
    }

    private static function directory(): string
    {
        return Yii::getAlias('@frontend/web') . self::WEB_DIR;
    }
}
