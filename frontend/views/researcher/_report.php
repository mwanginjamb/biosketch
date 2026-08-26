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


/*
 * =========================================================
 * REUSABLE INLINE STYLES
 * (kept as PHP variables purely to avoid retyping the same
 * inline CSS everywhere — NOT external classes. mPDF still
 * only ever sees plain inline style="" attributes.)
 * ========================================================= */

$colorHeading = '#1a365d';
$colorText = '#2d3748';
$colorMuted = '#4a5568';
$colorBorder = '#cbd5e0';
$colorBorderLt = '#e2e8f0';
$colorBgSoft = '#edf2f7';

$sSection = 'margin-top:16px; margin-bottom:3em;';
$sSectionTitle = "font-size:13px; font-weight:bold; color:{$colorHeading}; text-transform:uppercase; letter-spacing:0.5px; padding-bottom:4px; margin-bottom:8px; border-bottom:1.5px solid {$colorHeading};";

$sTable = 'width:100%; border-collapse:collapse; font-size:12.5px; color:' . $colorText . ';';
$sTh = 'text-align:left; background-color:' . $colorBgSoft . '; color:' . $colorHeading . '; padding:6px 8px; border:1px solid ' . $colorBorder . '; font-size:9.5px; text-transform:uppercase; letter-spacing:0.3px;';
$sTd = 'padding:6px 8px; border:1px solid ' . $colorBorderLt . '; vertical-align:top; font-size:11.5px;';
$sTdYear = $sTd . ' text-align:center; white-space:nowrap;';

?>

<div style="font-family: Helvetica, Arial, sans-serif; color: <?= $colorText ?>; font-size: 11.5px;">

    <!-- =========================================================
         HEADER
    ========================================================== -->

    <table style="width:100%; border-collapse:collapse; margin-bottom:10px;">
        <tr>

            <td style="width:90px; padding-right:14px; vertical-align:top;">

                <?php if ($profilePhoto): ?>

                    <img src="<?= Yii::getAlias('@webroot') . $profilePhoto ?>"
                        style="width:80px; height:80px; border:1px solid <?= $colorBorder ?>; object-fit:cover;"
                        alt="Researcher photograph">

                <?php else: ?>

                    <div
                        style="width:80px; height:80px; border:1px solid <?= $colorBorder ?>; background-color:<?= $colorBgSoft ?>; color:<?= $colorHeading ?>; font-size:28px; font-weight:bold; text-align:center; line-height:80px;">
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

            <td style="vertical-align:top;">

                <div style="font-size:16px; font-weight:bold; color:<?= $colorHeading ?>; margin-bottom:2px;">
                    <?= $value($fullName) ?>
                </div>

                <?php if ($researcher->role_title): ?>
                    <div style="font-size:11px; color:<?= $colorMuted ?>; margin-bottom:2px;">
                        <?= $value($researcher->role_title) ?>
                    </div>
                <?php endif; ?>

                <?php if ($researcher->primary_institution): ?>
                    <div style="font-size:10.5px; color:<?= $colorText ?>; margin-bottom:1px;">
                        <?= $value($researcher->primary_institution) ?>
                    </div>
                <?php endif; ?>

                <?php if ($researcher->department): ?>
                    <div style="font-size:10.5px; color:<?= $colorMuted ?>; margin-bottom:6px;">
                        <?= $value($researcher->department) ?>
                    </div>
                <?php endif; ?>

                <table
                    style="width:100%; border-collapse:collapse; font-size:9.5px; color:<?= $colorMuted ?>; margin-top:4px;">
                    <tr>

                        <?php if ($researcher->email): ?>
                            <td style="padding:2px 0;">
                                <strong>Email:</strong>
                                <?= $value($researcher->email) ?>
                            </td>
                        <?php endif; ?>

                        <?php if ($researcher->location): ?>
                            <td style="padding:2px 0;">
                                <strong>Location:</strong>
                                <?= $value($researcher->location) ?>
                            </td>
                        <?php endif; ?>

                    </tr>

                    <?php if ($researcher->website): ?>
                        <tr>
                            <td colspan="2" style="padding:2px 0;">
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

        <div style="<?= $sSection ?>">

            <div style="<?= $sSectionTitle ?>">
                Research Profile
            </div>

            <?php if (!empty($statement->statement_type)): ?>

                <div
                    style="font-size:10px; font-weight:bold; color:<?= $colorMuted ?>; margin-bottom:4px; text-transform:capitalize;">
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

                <div style="font-size:10.5px; line-height:1.5; color:<?= $colorText ?>; text-align:justify;">
                    <?= nl2br(Html::encode($statement->content)) ?>
                </div>

            <?php endif; ?>

        </div>

    <?php endif; ?>


    <!-- =========================================================
         RESEARCH INTERESTS / TAGS
    ========================================================== -->

    <?php if (!empty($researcher->research_tags)): ?>

        <div style="<?= $sSection ?>">

            <div style="<?= $sSectionTitle ?>">
                Research Areas
            </div>

            <div>
                <?php

                $tags = array_filter(
                    array_map(
                        'trim',
                        explode(',', $researcher->research_tags)
                    )
                );

                foreach ($tags as $tag):

                    ?>

                    <span
                        style="display:inline-block; background-color:<?= $colorBgSoft ?>; color:<?= $colorHeading ?>; font-size:9.5px; padding:3px 8px; margin:0 4px 4px 0; border:1px solid <?= $colorBorder ?>;">
                        <?= Html::encode($tag) ?>
                    </span>

                <?php endforeach; ?>
            </div>

        </div>

    <?php endif; ?>


    <!-- =========================================================
         EDUCATION & TRAINING
    ========================================================== -->

    <?php if (!empty($educations)): ?>

        <div style="<?= $sSection ?>">

            <div style="<?= $sSectionTitle ?>">
                Education &amp; Training
            </div>

            <table style="<?= $sTable ?>">

                <thead>
                    <tr>
                        <th style="<?= $sTh ?>">Degree</th>
                        <th style="<?= $sTh ?>">Field of Study</th>
                        <th style="<?= $sTh ?>">Institution</th>
                        <th style="<?= $sTh ?> text-align:center;">Year</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($educations as $education): ?>

                        <tr>

                            <td style="<?= $sTd ?> font-weight:bold;">
                                <?= $value($education->degree) ?>
                            </td>

                            <td style="<?= $sTd ?>">
                                <?= $value($education->field_of_study) ?>
                            </td>

                            <td style="<?= $sTd ?>">
                                <?= $value($education->institution_name) ?>
                            </td>

                            <td style="<?= $sTdYear ?>">
                                <?= $value($education->graduation_year) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>


    <!-- =========================================================
         RESEARCH IDENTIFIERS
    ========================================================== -->

    <?php if (!empty($identifiers)): ?>

        <div style="<?= $sSection ?>">

            <div style="<?= $sSectionTitle ?>">
                Researcher Identifiers
            </div>

            <table style="<?= $sTable ?>">

                <?php foreach ($identifiers as $identifier): ?>

                    <tr>

                        <td style="<?= $sTd ?> width:35%; font-weight:bold; color:<?= $colorHeading ?>;">
                            <?= $value(
                                $identifier->identifier_type
                            ) ?>
                        </td>

                        <td style="<?= $sTd ?>">
                            <?= $value(
                                $identifier->identifier_value
                            ) ?>
                        </td>

                        <?php if (
                            isset($identifier->verification_status)
                        ): ?>

                            <td style="<?= $sTd ?> width:20%; text-align:center;">

                                <?php if (
                                    (int) $identifier->verification_status === 1
                                ): ?>

                                    <span style="color:#2f855a; font-weight:bold; font-size:9.5px;">
                                        Verified
                                    </span>

                                <?php else: ?>

                                    <span style="color:#a0aec0; font-size:9.5px;">
                                        Unverified
                                    </span>

                                <?php endif; ?>

                            </td>

                        <?php endif; ?>

                    </tr>

                <?php endforeach; ?>

            </table>

        </div>

    <?php endif; ?>


    <!-- =========================================================
         GRANTS & FUNDING
    ========================================================== -->

    <?php if (!empty($grants)): ?>

        <div style="<?= $sSection ?>">

            <div style="<?= $sSectionTitle ?>">
                Grants &amp; Research Funding
            </div>

            <?php foreach ($grants as $grant): ?>

                <table style="width:100%; border-collapse:collapse; margin-bottom:8px; border:1px solid <?= $colorBorderLt ?>;">

                    <tr>

                        <td style="padding:8px 10px; vertical-align:top;">

                            <?php if (!empty($grant->grant_number)): ?>

                                <div style="font-size:9px; color:<?= $colorMuted ?>; margin-bottom:2px;">
                                    <?= $value(
                                        $grant->grant_number
                                    ) ?>
                                </div>

                            <?php endif; ?>

                            <div style="font-size:11px; font-weight:bold; color:<?= $colorHeading ?>; margin-bottom:4px;">
                                <?= $value($grant->title) ?>
                            </div>

                            <div style="font-size:9.5px; color:<?= $colorText ?>;">

                                <?php if (!empty($grant->role_id)): ?>

                                    <span style="margin-right:12px;">
                                        <strong>Role:</strong>
                                        <?= Html::encode($grant->roleOptions[$grant->role_id] ?? 'N/A') ?>
                                    </span>

                                <?php endif; ?>

                                <?php if (
                                    isset($grant->amount)
                                    && $grant->amount !== null
                                ): ?>

                                    <span style="margin-right:12px;">
                                        <strong>
                                            Award Amount:
                                        </strong>

                                        <?= \Yii::$app->formatter->asCurrency($grant->amount, $grant->currency) ?>
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

                        <td style="width:90px; padding:8px 10px; text-align:right; vertical-align:top;">

                            <?php if (!empty($grant->status)): ?>

                                <span
                                    style="display:inline-block; background-color:<?= $colorBgSoft ?>; color:<?= $colorHeading ?>; font-size:8.5px; font-weight:bold; padding:3px 7px; border:1px solid <?= $colorBorder ?>;">
                                    <?= Html::encode(
                                        strtoupper($grant->status)
                                    ) ?>
                                </span>

                            <?php endif; ?>

                        </td>

                    </tr>

                </table>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>


    <!-- =========================================================
         PUBLICATIONS
    ========================================================== -->

    <?php if (!empty($publications)): ?>

        <div style="<?= $sSection ?>">

            <div style="<?= $sSectionTitle ?>">
                Selected Publications
            </div>

            <?php foreach ($publications as $index => $publication): ?>

                <table style="width:100%; border-collapse:collapse; margin-bottom:8px;">
                    <tr>

                        <td
                            style="width:20px; padding-top:1px; vertical-align:top; font-size:10.5px; font-weight:bold; color:<?= $colorHeading ?>;">
                            <?= $index + 1 ?>.
                        </td>

                        <td style="vertical-align:top;">

                            <div style="font-size:10.5px; font-weight:bold; color:<?= $colorText ?>; margin-bottom:2px;">
                                <?= $value($publication->title) ?>
                            </div>

                            <div style="font-size:9.5px; color:<?= $colorMuted ?>; font-style:italic; margin-bottom:2px;">

                                <?php if (!empty($publication->journal)): ?>

                                    <span>
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

                                <div style="font-size:9px; color:<?= $colorMuted ?>;">
                                    DOI:
                                    <?= $value($publication->doi) ?>
                                </div>

                            <?php endif; ?>

                            <?php if (!empty($publication->pmid)): ?>

                                <div style="font-size:9px; color:<?= $colorMuted ?>;">
                                    PMID:
                                    <?= $value($publication->pmid) ?>
                                </div>

                            <?php endif; ?>

                        </td>

                    </tr>
                </table>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>


    <!-- =========================================================
         RESEARCH HIGHLIGHTS
    ========================================================== -->

    <?php if (
        !empty($researcher->major_breakthrough) ||
        !empty($researcher->patent_filed)
    ): ?>

        <div style="<?= $sSection ?>">

            <div style="<?= $sSectionTitle ?>">
                Research Highlights
            </div>

            <?php if (!empty($researcher->major_breakthrough)): ?>

                <div style="margin-bottom:8px;">

                    <div style="font-size:10px; font-weight:bold; color:<?= $colorHeading ?>; margin-bottom:2px;">
                        Major Research Breakthrough
                    </div>

                    <div style="font-size:10.5px; line-height:1.5; color:<?= $colorText ?>;">
                        <?= nl2br(
                            Html::encode(
                                $researcher->major_breakthrough
                            )
                        ) ?>
                    </div>

                </div>

            <?php endif; ?>


            <?php if (!empty($researcher->patent_filed)): ?>

                <div style="margin-bottom:8px;">

                    <div style="font-size:10px; font-weight:bold; color:<?= $colorHeading ?>; margin-bottom:2px;">
                        Patents &amp; Intellectual Property
                    </div>

                    <div style="font-size:10.5px; line-height:1.5; color:<?= $colorText ?>;">
                        <?= nl2br(
                            Html::encode(
                                $researcher->patent_filed
                            )
                        ) ?>
                    </div>

                </div>

            <?php endif; ?>

        </div>

    <?php endif; ?>


    <!-- =========================================================
         MEDIA / RESEARCH OUTPUTS
    ========================================================== -->

    <?php if (!empty($media)): ?>

        <div style="<?= $sSection ?>">

            <div style="<?= $sSectionTitle ?>">
                Research Outputs &amp; Media
            </div>

            <?php foreach ($media as $item): ?>

                <div style="margin-bottom:8px; padding-bottom:8px; border-bottom:1px solid <?= $colorBorderLt ?>;">

                    <?php if (!empty($item->title)): ?>

                        <div style="font-size:10.5px; font-weight:bold; color:<?= $colorText ?>; margin-bottom:2px;">
                            <?= $value($item->title) ?>
                        </div>

                    <?php endif; ?>

                    <?php if (!empty($item->description)): ?>

                        <div style="font-size:10px; line-height:1.5; color:<?= $colorText ?>; margin-bottom:2px;">
                            <?= nl2br(
                                Html::encode($item->description)
                            ) ?>
                        </div>

                    <?php endif; ?>

                    <?php if (!empty($item->url)): ?>

                        <div style="font-size:9px; color:<?= $colorMuted ?>;">
                            <?= $value($item->url) ?>
                        </div>

                    <?php endif; ?>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>



</div>