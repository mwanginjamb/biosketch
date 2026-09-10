<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use frontend\assets\AppAsset;

AppAsset::register($this);
$this->params['meta_description'] = 'BioSketch Professional is a web application that allows researchers to create and manage their biosketches for grant applications. It provides a user-friendly interface to input personal information, education, work experience, publications, and other relevant details. The application also generates formatted biosketch documents that comply with funding agency requirements.';
$this->params['meta_keywords'] = 'BioSketch, Professional, Researcher, Biosketch, Grant Applications, Web Application, User-Friendly Interface, Personal Information, Education, Work Experience, Publications, Formatted Documents, Funding Agency Requirements';


$this->registerCsrfMetaTags();
$this->registerMetaTag(
    ['charset' => Yii::$app->charset],
    'charset',
);
$this->registerMetaTag(
    [
        'name' => 'viewport',
        'content' => 'width=device-width, initial-scale=1',
    ],
);
if (!empty($this->params['meta_description'])) {
    $this->registerMetaTag(
        [
            'name' => 'description',
            'content' => $this->params['meta_description'],
        ],
    );
}
if (!empty($this->params['meta_keywords'])) {
    $this->registerMetaTag(
        [
            'name' => 'keywords',
            'content' => $this->params['meta_keywords'],
        ],
    );
}
$this->registerLinkTag(
    [
        'rel' => 'icon',
        'type' => 'image/png',
        'href' => Yii::getAlias('@web/icons/icon-32x32.png'),
    ],
);


