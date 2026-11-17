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

            ['staffID', 'trim'],
            ['staffID', 'string', 'max' => 50],
            [
                'staffID',
                'unique',
                'targetClass' => User::class,
                'targetAttribute' => 'staff_id',
                'message' => 'An account already exists for this employee number.',
            ],
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

        // Persist employee details only when verified against the staff directory,
        // never from values posted by the browser.
        $employee = $this->staffID !== '' ? $this->findEmployee($this->staffID) : null;
        if ($employee !== null) {
            $user->staff_id = $employee['staffID'];
            $user->department = $employee['department'];
            $user->department_code = $employee['departmentCode'];
        }

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

    /**
     * Looks up a single employee in the staff directory.
     *
     * @return array{staffID: string, email: string, fullNames: string, displayName: string, department: string|null, departmentCode: string|null}|null
     *         null when the employee is not found or the directory is unavailable.
     */
    public function findEmployee(string $staffID): ?array
    {
        $staffID = trim($staffID);
        if ($staffID === '') {
            return null;
        }

        try {
            $assignees = $this->fetchAssignees();
        } catch (\Throwable $e) {
            Yii::error('Staff directory lookup failed: ' . $e->getMessage(), __METHOD__);
            return null;
        }

        if (!is_array($assignees)) {
            return null;
        }

        foreach ($assignees as $key => $displayName) {
            $employee = self::parseAssignee((string) $key, (string) $displayName);
            if ($employee !== null && strcasecmp($employee['staffID'], $staffID) === 0) {
                return $employee;
            }
        }

        return null;
    }

    /**
     * Parses one staff directory entry.
     *
     * Key:   "90296 - jlangat@kemri.go.ke - DR.JOAN CHERONO LANGAT"
     * Value: "90296 - DR.JOAN CHERONO LANGAT - SSRC - DIR005"
     *        "EMP11727 - Edwin Kaibei Test - No Dept. - "   (no department)
     *
     * Fields are separated by " - " (with spaces) so hyphenated emails/names survive.
     */
    public static function parseAssignee(string $key, string $value): ?array
    {
        $keyParts = array_map('trim', explode(' - ', $key, 3));
        if (count($keyParts) < 3 || $keyParts[0] === '') {
            return null;
        }
        [$staffID, $email, $fullNames] = $keyParts;

        $department = null;
        $departmentCode = null;
        $valueParts = array_map('trim', explode(' - ', $value));
        if (count($valueParts) >= 4) {
            $department = $valueParts[count($valueParts) - 2];
            $departmentCode = $valueParts[count($valueParts) - 1];
        }

        // Directory placeholder for staff without a department.
        if ($department === '' || strcasecmp((string) $department, 'No Dept.') === 0) {
            $department = null;
        }
        if ($departmentCode === '') {
            $departmentCode = null;
        }

        return [
            'staffID' => $staffID,
            'email' => strtolower($email),
            'fullNames' => $fullNames,
            'displayName' => $value,
            'department' => $department,
            'departmentCode' => $departmentCode,
        ];
    }
}
