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
            ['email', 'validateEmailMatchesDirectory', 'skipOnError' => true],
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
        $employee = $this->resolveEmployee();
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



    /** How long a successful staff directory download is cached, in seconds. */
    private const DIRECTORY_CACHE_TTL = 600;
    private const DIRECTORY_CACHE_KEY = 'staff-directory';

    /** @var array|null memoized result of the directory lookup for $staffID */
    private ?array $employee = null;
    private bool $employeeResolved = false;

    /**
     * Fetches the staff directory (cached).
     *
     * The JSON format is an object of
     *   "<staff no> - <email> - <full name>": "<staff no> - <full name> - <department> - <department code>"
     *
     * TLS certificates are verified unless ASSIGNEE_VERIFY_SSL=false (local development only).
     * Set ASSIGNEE_CA_BUNDLE to a PEM file path when the endpoint uses an internal CA.
     *
     * @return array|null the directory, or null when it could not be fetched.
     */
    public function fetchAssignees()
    {
        $cache = Yii::$app->cache;
        $cached = $cache?->get(self::DIRECTORY_CACHE_KEY);
        if (is_array($cached)) {
            return $cached;
        }

        $verifySsl = filter_var(env('ASSIGNEE_VERIFY_SSL', 'true'), FILTER_VALIDATE_BOOLEAN);
        $curlOptions = [
            CURLOPT_SSL_VERIFYPEER => $verifySsl,
            CURLOPT_SSL_VERIFYHOST => $verifySsl ? 2 : 0,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 30,
        ];
        $caBundle = env('ASSIGNEE_CA_BUNDLE');
        if ($verifySsl && $caBundle) {
            $curlOptions[CURLOPT_CAINFO] = $caBundle;
        }

        $client = new Client([
            'transport' => CurlTransport::class,
        ]);

        $response = $client->createRequest()
            ->setMethod('GET')
            ->setUrl(env('ASSIGNEE_ENDPOINT'))
            ->addHeaders(['Accept' => 'application/json'])
            ->setOptions($curlOptions)
            ->send();

        if (!$response->isOk || !is_array($response->data)) {
            Yii::warning('Staff directory returned HTTP ' . $response->statusCode, __METHOD__);
            return null;
        }

        $cache?->set(self::DIRECTORY_CACHE_KEY, $response->data, self::DIRECTORY_CACHE_TTL);

        return $response->data;
    }

    /**
     * Inline validator: when the employee number is found in the directory, the
     * email must be the one on that staff record. This stops someone claiming
     * another employee's number with their own email address.
     */
    public function validateEmailMatchesDirectory(string $attribute): void
    {
        $employee = $this->resolveEmployee();
        if ($employee !== null && strcasecmp($employee['email'], $this->email) !== 0) {
            $this->addError($attribute, 'Email must match the staff directory record for this employee number.');
        }
    }

    /** Looks up $staffID once per form instance. */
    private function resolveEmployee(): ?array
    {
        if (!$this->employeeResolved) {
            $this->employee = $this->staffID !== '' ? $this->findEmployee($this->staffID) : null;
            $this->employeeResolved = true;
        }

        return $this->employee;
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
