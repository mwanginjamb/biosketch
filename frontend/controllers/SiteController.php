<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\components\RbacHelper;
use common\components\Throttle;
use common\models\LoginForm;
use common\models\User;
use frontend\models\ContactForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\Researcher;
use frontend\models\ResendVerificationEmailForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\captcha\CaptchaAction;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\mail\MailerInterface;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly MailerInterface $mailer,
        $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'signup', 'lookup-employee', 'index', 'users', 'view', 'create', 'update', 'delete'],
                'rules' => [
                    [
                        // Employee lookup only exists to prefill the signup form.
                        'actions' => ['signup', 'lookup-employee'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        // System users list is part of RBAC administration.
                        'actions' => ['users'],
                        'allow' => true,
                        'roles' => [RbacHelper::PERM_MANAGE_RBAC],
                    ],
                    [
                        'actions' => ['logout', 'index', 'view', 'create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
            'captcha' => [
                'class' => CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex(): string|Response
    {
        // drd and admin see the list of all biosketches; researchers go to their own landing page.
        if (Yii::$app->user->can(RbacHelper::PERM_VIEW_LIST)) {
            $this->layout = 'list';
            // Get All researcher models
            $researchers = Researcher::find()->with('user')->all();
            return $this->render('index', [
                'models' => $researchers
            ]);
        } else { // Access home route
            return $this->redirect(Url::toRoute(['site/home']));
        }
    }

    /**
     * Lists system users with their roles (RBAC administration).
     */
    public function actionUsers(): string
    {
        $this->layout = 'list';

        return $this->render('users', [
            'users' => User::find()->orderBy(['username' => SORT_ASC])->all(),
        ]);
    }

    public function actionHome(): string
    {
        $this->layout = 'plain';
        return $this->render('home');
    }

    /** Video file, relative to the web root (frontend/web). */
    public const TUTORIAL_VIDEO = 'media/biosketch-tutorial.mp4';

    /**
     * /tutorial: the instructional video. Public, like the home page, so new staff
     * can watch it before signing up. The file is served statically by the web server
     * (which handles byte ranges for seeking); this action only renders the player.
     */
    public function actionTutorial(): string
    {
        $this->layout = 'plain';
        $this->view->title = 'Tutorial';

        $file = Yii::getAlias('@frontend/web/' . self::TUTORIAL_VIDEO);

        return $this->render('tutorial', [
            'videoUrl' => is_file($file)
                ? Url::to('@web/' . self::TUTORIAL_VIDEO) . '?v=' . filemtime($file)   // bust caches on replace
                : null,
        ]);
    }

    /**
     * Logs in a user.
     *
     * @return string|Response
     */
    public function actionLogin(): string|Response
    {
        $this->layout = 'guest';
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logs out the current user.
     *
     * @return Response
     */
    public function actionLogout(): Response
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return string|Response
     */
    public function actionContact(): string|Response
    {
        $model = new ContactForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $sent = $model->sendEmail(
                $this->mailer,
                Yii::$app->params['adminEmail'],
                Yii::$app->params['senderEmail'],
                Yii::$app->params['senderName'],
            );

            if ($sent) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        }

        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout(): string
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return string|Response
     */
    public function actionSignup(): string|Response
    {
        $this->layout = 'guest';
        $model = new SignupForm();

        $signed = $model->load(Yii::$app->request->post()) && $model->signup(
            $this->mailer,
            Yii::$app->params['supportEmail'],
            Yii::$app->name,
        );

        if ($signed) {
            Yii::$app->session->setFlash('success', 'Thank you for registration. Please check your inbox for verification email.');
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return string|Response
     */
    public function actionRequestPasswordReset(): string|Response
    {
        $this->layout = 'guest';
        $model = new PasswordResetRequestForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $sent = $model->sendEmail(
                $this->mailer,
                Yii::$app->params['supportEmail'],
                Yii::$app->name,
            );

            if ($sent) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            }

            Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return string|Response
     * @throws BadRequestHttpException
     */
    public function actionResetPassword(string $token): string|Response
    {
        $this->layout = 'guest';
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionVerifyEmail(string $token): Response
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->verifyEmail()) {
            Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
            return $this->goHome();
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return string|Response
     */
    public function actionResendVerificationEmail(): string|Response
    {
        $this->layout = 'guest';
        $model = new ResendVerificationEmailForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $sent = $model->sendEmail(
                $this->mailer,
                Yii::$app->params['supportEmail'],
                Yii::$app->name,
            );

            if ($sent) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }

            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model,
        ]);
    }



    public function actionLookupEmployee($employeeNumber)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!Throttle::hit('lookup-employee', (string) Yii::$app->request->userIP, self::LOOKUP_LIMIT, self::LOOKUP_WINDOW)) {
            Yii::$app->response->statusCode = 429;
            return [
                'success' => false,
                'message' => 'Too many lookups. Please wait a few minutes and try again.'
            ];
        }

        $employee = (new SignupForm())->findEmployee((string) $employeeNumber);

        if ($employee === null) {
            return [
                'success' => false,
                'message' => 'Employee not found'
            ];
        }

        // Return only what the signup form prefills, to limit directory exposure.
        return [
            'success' => true,
            'username' => strtolower((string) strstr($employee['email'], '@', true)),
            'email' => $employee['email'],
        ];
    }

    /** Max employee lookups per client IP within LOOKUP_WINDOW seconds. */
    private const LOOKUP_LIMIT = 20;
    private const LOOKUP_WINDOW = 600;
}
