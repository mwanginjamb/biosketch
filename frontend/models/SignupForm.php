<?php

declare(strict_types=1);

namespace frontend\models;

use common\models\User;
use Yii;
use yii\base\Model;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\mail\MailerInterface;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public string $username = '';
    public string $email = '';
    public string $password = '';
    public string $passwordConfirm = '';
    public string $staffID = '';
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => User::class, 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => User::class, 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength']],
            ['passwordConfirm', 'required'],
            ['passwordConfirm', 'compare', 'compareAttribute' => 'password', 'message' => 'Passwords do not match.'],

            ['staffID', 'safe'],
        ];
    }

    /**
     * Signs user up.
     *
     * @param MailerInterface $mailer the mailer component.
     * @param string $supportEmail the support email address.
     * @param string $appName the application name.
     *
     * @return bool|null whether the creating new account was successful and email was sent.
     */
    public function signup(MailerInterface $mailer, string $supportEmail, string $appName): bool|null
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();

        $user->username = $this->username;
        $user->email = $this->email;

        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();

        return $user->save() && $this->sendEmail($mailer, $user, $supportEmail, $appName);
    }

    /**
     * Sends confirmation email to user.
     *
     * @param MailerInterface $mailer the mailer component.
     * @param User $user user model to which the email should be sent.
     * @param string $supportEmail the support email address.
     * @param string $appName the application name.
     *
     * @return bool whether the email was sent.
     */
    protected function sendEmail(MailerInterface $mailer, User $user, string $supportEmail, string $appName): bool
    {
        return $mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user],
            )
            ->setFrom([$supportEmail => $appName . ' robot'])
            ->setTo($this->email)
            ->setSubject('Account registration at ' . $appName)
            ->send();
    }



    /* Make a Get request for assignes
     * The JSON format is:
     * {
     *   "90254 - melvineobuya@gmail.com": "OBUYA",
     *  "90252 - lauraombogo@gmail.com": "LORRAINE",
     * }
     */

    public function fetchAssignees()
    {
        $endpoint = env('ASSIGNEE_ENDPOINT');
        $client = new Client([
            'transport' => CurlTransport::class,
        ]);

        $request = $client->createRequest()
            ->setMethod('GET')
            ->setUrl($endpoint)
            ->addHeaders(['Content-Type' => 'application/json'])
            ->setFormat(Client::FORMAT_JSON)  // Ensures JSON encoding for request
            ->setOptions([
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            ]);

        $response = $request->send();
        Yii::info('Raw response content: ' . $response->content, 'api_debug');
        if ($response->isOk) { // Check if the response status is 200-299
            return $response->data; // Return the relevant response data
        } else {
            // Log error details if needed and return a clear message
            return [
                'status' => $response->statusCode,
                'error' => $response->data ?? 'Unexpected error occurred'
            ];
        }
    }
}
