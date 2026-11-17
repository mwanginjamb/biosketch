<?php

declare(strict_types=1);

namespace frontend\components;

use common\components\RbacHelper;
use frontend\models\Researcher;
use Yii;
use yii\db\ActiveRecord;
use yii\web\User as WebUser;

/**
 * Builds the notification-bell items for a researcher: biosketch sections that are
 * missing data, and sections that have not been updated for a long time.
 *
 * Items are computed on each request from the researcher's own data (nothing is stored),
 * so they disappear as soon as the section is completed or updated.
 *
 * Each item: [
 *   'key'    => string  stable id, e.g. 'missing-statement'
 *   'level'  => 'action'|'suggestion'|'stale'
 *   'title'  => string
 *   'detail' => string
 *   'icon'   => string  Material Symbols name
 *   'url'    => array   route to the relevant form section
 * ]
 */
final class BiosketchChecklist
{
    /** Default age (days) after which a section counts as not updated. Override with params['biosketch.staleAfterDays']. */
    public const DEFAULT_STALE_AFTER_DAYS = 365;

    public const LEVEL_ACTION = 'action';
    public const LEVEL_SUGGESTION = 'suggestion';
    public const LEVEL_STALE = 'stale';

    /** Personal-information fields that should be filled in, with their display names. */
    private const PERSONAL_FIELDS = [
        'primary_institution' => 'primary institution',
        'department' => 'department',
        'role_title' => 'role/title',
        'email' => 'contact email',
        'orcid' => 'ORCID',
        'research_tags' => 'research areas',
    ];

    /**
     * Items for the logged-in user. Only users with the researcher role (createBiosketch) get any.
     *
     * @return array<int, array{key: string, level: string, title: string, detail: string, icon: string, url: array}>
     */
    public static function forUser(WebUser $webUser): array
    {
        if ($webUser->isGuest || !$webUser->can(RbacHelper::PERM_CREATE)) {
            return [];
        }

        $researcher = $webUser->identity->researcher;
        if ($researcher === null) {
            return [[
                'key' => 'create-biosketch',
                'level' => self::LEVEL_ACTION,
                'title' => 'Create your BioSketch',
                'detail' => 'You have not started your biosketch yet.',
                'icon' => 'add_circle',
                'url' => ['/researcher/create'],
            ]];
        }

        return self::forResearcher($researcher, time());
    }

    /**
     * @return array<int, array{key: string, level: string, title: string, detail: string, icon: string, url: array}>
     */
    public static function forResearcher(Researcher $researcher, int $now): array
    {
        $staleAfter = (int) (Yii::$app->params['biosketch.staleAfterDays'] ?? self::DEFAULT_STALE_AFTER_DAYS) * 86400;
        $section = static fn(string $anchor): array => ['/researcher/update', 'id' => $researcher->id, '#' => $anchor];
        $items = [];

        // ── Missing data ───────────────────────────────────────────────────────
        if ((int) $researcher->status !== 1) {
            $items[] = self::item('draft', self::LEVEL_ACTION, 'Your BioSketch is a draft',
                'Set the status to Active to publish it once it is complete.', 'edit_note', $section('section-personal'));
        }

        if (!$researcher->profile_photo) {
            $items[] = self::item('missing-photo', self::LEVEL_SUGGESTION, 'Add a profile photo',
                'Profiles with a photo are easier to recognise.', 'add_a_photo', $section('section-personal'));
        }

        $missing = [];
        foreach (self::PERSONAL_FIELDS as $attribute => $label) {
            if (trim((string) $researcher->$attribute) === '') {
                $missing[] = $label;
            }
        }
        if ($missing) {
            $items[] = self::item('missing-personal', self::LEVEL_ACTION, 'Complete your personal information',
                'Missing: ' . implode(', ', $missing) . '.', 'person', $section('section-personal'));
        }

        $statement = $researcher->researcherStatement;
        if ($statement === null || trim((string) $statement->content) === '') {
            $items[] = self::item('missing-statement', self::LEVEL_ACTION, 'Add your research statement',
                'A short statement about your research interests and expertise.', 'description', $section('section-statement'));
        }

        $collections = [
            // relation => [key, level, title, detail, icon, anchor]
            'researcherEducations' => ['education', self::LEVEL_ACTION, 'Education & Training',
                'Add at least one degree or qualification.', 'school', 'section-education'],
            'publications' => ['publications', self::LEVEL_ACTION, 'Publications',
                'List your key publications.', 'menu_book', 'section-publications'],
            'researcherIdentifiers' => ['identifiers', self::LEVEL_SUGGESTION, 'Researcher Identifiers',
                'Add identifiers such as Scopus or eRA Commons.', 'badge', 'section-identifiers'],
            'researcherGrants' => ['grants', self::LEVEL_SUGGESTION, 'Grants & Funding',
                'Add current or past research grants.', 'monetization_on', 'section-grants'],
        ];
        foreach ($collections as $relation => [$key, $level, $title, $detail, $icon, $anchor]) {
            if ($researcher->$relation === []) {
                $items[] = self::item("missing-{$key}", $level, "Add {$title}", $detail, $icon, $section($anchor));
            }
        }

        if (trim((string) $researcher->major_breakthrough) === '' && trim((string) $researcher->patent_filed) === '') {
            $items[] = self::item('missing-accomplishments', self::LEVEL_SUGGESTION, 'Add accomplishments',
                'Describe a major breakthrough or patents filed.', 'emoji_events', $section('section-accomplishments'));
        }

        // ── Not updated recently ──────────────────────────────────────────────
        // Rows saved before section timestamps existed fall back to the profile's own updated_at.
        $profileUpdated = (int) ($researcher->updated_at ?? $researcher->created_at ?? 0);
        $stale = [
            ['personal', 'Personal information', [$researcher], 'section-personal'],
            ['statement', 'Research statement', $statement ? [$statement] : [], 'section-statement'],
            ['education', 'Education & Training', $researcher->researcherEducations, 'section-education'],
            ['publications', 'Publications', $researcher->publications, 'section-publications'],
            ['identifiers', 'Researcher Identifiers', $researcher->researcherIdentifiers, 'section-identifiers'],
            ['grants', 'Grants & Funding', $researcher->researcherGrants, 'section-grants'],
        ];
        foreach ($stale as [$key, $title, $records, $anchor]) {
            if ($records === []) {
                continue;   // already reported as missing
            }
            $lastUpdated = self::lastUpdated($records, $profileUpdated);
            if ($lastUpdated > 0 && $now - $lastUpdated >= $staleAfter) {
                $items[] = self::item("stale-{$key}", self::LEVEL_STALE, "Review {$title}",
                    'Last updated ' . Yii::$app->formatter->asRelativeTime($lastUpdated, $now) . '.', 'update', $section($anchor));
            }
        }

        return $items;
    }

    /** @param ActiveRecord[] $records */
    private static function lastUpdated(array $records, int $fallback): int
    {
        $latest = 0;
        foreach ($records as $record) {
            $latest = max($latest, (int) ($record->updated_at ?? $record->created_at ?? $fallback));
        }

        return $latest;
    }

    private static function item(string $key, string $level, string $title, string $detail, string $icon, array $url): array
    {
        return compact('key', 'level', 'title', 'detail', 'icon', 'url');
    }
}
