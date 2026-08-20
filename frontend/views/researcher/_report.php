<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var frontend\models\Researcher $biosketch
 */

$researcher = $biosketch;

/*
 * Convenience helpers
 */
$fullName = trim(
    ($researcher->title ? $researcher->title . ' ' : '') .
    ($researcher->full_name ?? '')
);

$profilePhoto = $researcher->profile_photo ?? null;

/*
 * Helper for safely displaying values.
 */
$value = static function ($value, $fallback = '—') {
    return ($value !== null && $value !== '')
        ? Html::encode($value)
        : $fallback;
};

/*
 * Researcher statement
 */
$statement = $researcher->researcherStatement ?? null;

/*
 * Collections
 */
$educations = $researcher->researcherEducations ?? [];
$publications = $researcher->publications ?? [];
$identifiers = $researcher->researcherIdentifiers ?? [];
$media = $researcher->researcherMedia ?? [];
$grants = $researcher->researcherGrants ?? [];

?>

<div class="biosketch">

    <!-- =========================================================
         HEADER
    ========================================================== -->

    <table class="profile-header">
        <tr>

            <td class="profile-photo-cell">

                <?php if ($profilePhoto): ?>

                    <img src="<?= Yii::getAlias('@webroot') . $profilePhoto ?>" class="profile-photo"
                        alt="Researcher photograph">

                <?php else: ?>

                    <div class="profile-photo-placeholder">
                        <?= Html::encode(
                            strtoupper(
                                mb_substr(
                                    $researcher->full_name ?? 'R',
                                    0,
                                    1
                                )
                            )
                        ) ?>
                    </div>

                <?php endif; ?>

            </td>

            <td class="profile-identity">

                <div class="profile-name">
                    <?= $value($fullName) ?>
                </div>

                <?php if ($researcher->role_title): ?>
                    <div class="profile-role">
                        <?= $value($researcher->role_title) ?>
                    </div>
                <?php endif; ?>

                <?php if ($researcher->primary_institution): ?>
                    <div class="profile-institution">
                        <?= $value($researcher->primary_institution) ?>
                    </div>
                <?php endif; ?>

                <?php if ($researcher->department): ?>
                    <div class="profile-department">
                        <?= $value($researcher->department) ?>
                    </div>
                <?php endif; ?>

                <table class="contact-table">
                    <tr>

                        <?php if ($researcher->email): ?>
                            <td>
                                <strong>Email:</strong>
                                <?= $value($researcher->email) ?>
                            </td>
                        <?php endif; ?>

                        <?php if ($researcher->location): ?>
                            <td>
                                <strong>Location:</strong>
                                <?= $value($researcher->location) ?>
                            </td>
                        <?php endif; ?>

                    </tr>

                    <?php if ($researcher->website): ?>
                        <tr>
                            <td colspan="2">
                                <strong>Web:</strong>
                                <?= $value($researcher->website) ?>
                            </td>
                        </tr>
                    <?php endif; ?>

                </table>

            </td>

        </tr>
    </table>


    <!-- =========================================================
         RESEARCH PROFILE
    ========================================================== -->

    <?php if ($statement): ?>

        <section class="report-section">

            <h2 class="section-title">
                Research Profile
            </h2>

            <?php if (!empty($statement->statement_type)): ?>

                <div class="statement-type">
                    <?= Html::encode(
                        ucwords(
                            str_replace(
                                '_',
                                ' ',
                                $statement->statement_type
                            )
                        )
                    ) ?>
                </div>

            <?php endif; ?>

            <?php if (!empty($statement->content)): ?>

                <div class="statement-content">
                    <?= nl2br(Html::encode($statement->content)) ?>
                </div>

            <?php endif; ?>

        </section>

    <?php endif; ?>


    <!-- =========================================================
         RESEARCH INTERESTS / TAGS
    ========================================================== -->

    <?php if (!empty($researcher->research_tags)): ?>

        <section class="report-section">

            <h2 class="section-title">
                Research Areas
            </h2>

            <div class="research-tags">
                <?php

                $tags = array_filter(
                    array_map(
                        'trim',
                        explode(',', $researcher->research_tags)
                    )
                );

                foreach ($tags as $tag):

                    ?>

                    <span class="research-tag">
                        <?= Html::encode($tag) ?>
                    </span>

                <?php endforeach; ?>
            </div>

        </section>

    <?php endif; ?>


    <!-- =========================================================
         EDUCATION & TRAINING
    ========================================================== -->

    <?php if (!empty($educations)): ?>

        <section class="report-section">

            <h2 class="section-title">
                Education &amp; Training
            </h2>

            <table class="data-table education-table">

                <thead>
                    <tr>
                        <th>Degree</th>
                        <th>Field of Study</th>
                        <th>Institution</th>
                        <th>Year</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($educations as $education): ?>

                        <tr>

                            <td class="strong">
                                <?= $value($education->degree) ?>
                            </td>

                            <td>
                                <?= $value($education->field_of_study) ?>
                            </td>

                            <td>
                                <?= $value($education->institution_name) ?>
                            </td>

                            <td class="year">
                                <?= $value($education->graduation_year) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </section>

    <?php endif; ?>


    <!-- =========================================================
         RESEARCH IDENTIFIERS
    ========================================================== -->

    <?php if (!empty($identifiers)): ?>

        <section class="report-section">

            <h2 class="section-title">
                Researcher Identifiers
            </h2>

            <table class="identifier-table">

                <?php foreach ($identifiers as $identifier): ?>

                    <tr>

                        <td class="identifier-type">
                            <?= $value(
                                $identifier->identifier_type
                            ) ?>
                        </td>

                        <td class="identifier-value">
                            <?= $value(
                                $identifier->identifier_value
                            ) ?>
                        </td>

                        <?php if (
                            isset($identifier->verification_status)
                        ): ?>

                            <td class="identifier-status">

                                <?php if (
                                    (int) $identifier->verification_status === 1
                                ): ?>

                                    <span class="verified">
                                        Verified
                                    </span>

                                <?php else: ?>

                                    <span class="unverified">
                                        Unverified
                                    </span>

                                <?php endif; ?>

                            </td>

                        <?php endif; ?>

                    </tr>

                <?php endforeach; ?>

            </table>

        </section>

    <?php endif; ?>


    <!-- =========================================================
         GRANTS & FUNDING
    ========================================================== -->

    <?php if (!empty($grants)): ?>

        <section class="report-section">

            <h2 class="section-title">
                Grants &amp; Research Funding
            </h2>

            <?php foreach ($grants as $grant): ?>

                <div class="grant-item">

                    <table class="grant-table">

                        <tr>

                            <td class="grant-main">

                                <?php if (!empty($grant->grant_number)): ?>

                                    <div class="grant-number">
                                        <?= $value(
                                            $grant->grant_number
                                        ) ?>
                                    </div>

                                <?php endif; ?>

                                <div class="grant-title">
                                    <?= $value($grant->title) ?>
                                </div>

                                <div class="grant-details">

                                    <?php if (!empty($grant->role)): ?>

                                        <span>
                                            <strong>Role:</strong>
                                            <?= $value($grant->role) ?>
                                        </span>

                                    <?php endif; ?>

                                    <?php if (
                                        isset($grant->total_award)
                                        && $grant->total_award !== null
                                    ): ?>

                                        <span>
                                            <strong>
                                                Total Award:
                                            </strong>

                                            <?= Yii::$app->formatter->asCurrency(
                                                $grant->total_award
                                            ) ?>
                                        </span>

                                    <?php endif; ?>

                                    <?php if (!empty($grant->funding_agency)): ?>

                                        <span>
                                            <strong>Funder:</strong>
                                            <?= $value(
                                                $grant->funding_agency
                                            ) ?>
                                        </span>

                                    <?php endif; ?>

                                </div>

                            </td>

                            <td class="grant-status-cell">

                                <?php if (!empty($grant->status)): ?>

                                    <span class="grant-status">
                                        <?= Html::encode(
                                            strtoupper($grant->status)
                                        ) ?>
                                    </span>

                                <?php endif; ?>

                            </td>

                        </tr>

                    </table>

                </div>

            <?php endforeach; ?>

        </section>

    <?php endif; ?>


    <!-- =========================================================
         PUBLICATIONS
    ========================================================== -->

    <?php if (!empty($publications)): ?>

        <section class="report-section">

            <h2 class="section-title">
                Selected Publications
            </h2>

            <?php foreach ($publications as $index => $publication): ?>

                <div class="publication-item">

                    <div class="publication-number">
                        <?= $index + 1 ?>.
                    </div>

                    <div class="publication-content">

                        <div class="publication-title">

                            <?= $value($publication->title) ?>

                        </div>

                        <div class="publication-meta">

                            <?php if (!empty($publication->journal)): ?>

                                <span class="journal">
                                    <?= $value($publication->journal) ?>
                                </span>

                            <?php endif; ?>

                            <?php if (
                                !empty($publication->publication_year)
                            ): ?>

                                <span>
                                    (
                                    <?= $value(
                                        $publication->publication_year
                                    ) ?>)
                                </span>

                            <?php endif; ?>

                        </div>

                        <?php if (!empty($publication->doi)): ?>

                            <div class="publication-identifier">
                                DOI:
                                <?= $value($publication->doi) ?>
                            </div>

                        <?php endif; ?>

                        <?php if (!empty($publication->pmid)): ?>

                            <div class="publication-identifier">
                                PMID:
                                <?= $value($publication->pmid) ?>
                            </div>

                        <?php endif; ?>

                    </div>

                </div>

            <?php endforeach; ?>

        </section>

    <?php endif; ?>


    <!-- =========================================================
         RESEARCH HIGHLIGHTS
    ========================================================== -->

    <?php if (
        !empty($researcher->major_breakthrough) ||
        !empty($researcher->patent_filed)
    ): ?>

        <section class="report-section">

            <h2 class="section-title">
                Research Highlights
            </h2>

            <?php if (!empty($researcher->major_breakthrough)): ?>

                <div class="highlight-item">

                    <div class="highlight-heading">
                        Major Research Breakthrough
                    </div>

                    <div>
                        <?= nl2br(
                            Html::encode(
                                $researcher->major_breakthrough
                            )
                        ) ?>
                    </div>

                </div>

            <?php endif; ?>


            <?php if (!empty($researcher->patent_filed)): ?>

                <div class="highlight-item">

                    <div class="highlight-heading">
                        Patents &amp; Intellectual Property
                    </div>

                    <div>
                        <?= nl2br(
                            Html::encode(
                                $researcher->patent_filed
                            )
                        ) ?>
                    </div>

                </div>

            <?php endif; ?>

        </section>

    <?php endif; ?>


    <!-- =========================================================
         MEDIA / RESEARCH OUTPUTS
    ========================================================== -->

    <?php if (!empty($media)): ?>

        <section class="report-section">

            <h2 class="section-title">
                Research Outputs &amp; Media
            </h2>

            <?php foreach ($media as $item): ?>

                <div class="media-item">

                    <?php if (!empty($item->title)): ?>

                        <div class="media-title">
                            <?= $value($item->title) ?>
                        </div>

                    <?php endif; ?>

                    <?php if (!empty($item->description)): ?>

                        <div class="media-description">
                            <?= nl2br(
                                Html::encode($item->description)
                            ) ?>
                        </div>

                    <?php endif; ?>

                    <?php if (!empty($item->url)): ?>

                        <div class="media-url">
                            <?= $value($item->url) ?>
                        </div>

                    <?php endif; ?>

                </div>

            <?php endforeach; ?>

        </section>

    <?php endif; ?>


    <!-- =========================================================
         REPORT FOOTER
    ========================================================== -->

    <div class="report-end">

        <div>
            Researcher BioSketch
        </div>

        <div>
            Generated
            <?= date('d M Y') ?>
        </div>

    </div>

</div>