<?php

declare(strict_types=1);

namespace frontend\tests\Unit\Models;

use Codeception\Test\Unit;
use common\fixtures\UserFixture;
use common\models\User;
use frontend\models\SignupForm;
use frontend\tests\Support\UnitTester;
use Yii;
use yii\mail\MessageInterface;

final class SignupFormTest extends Unit
{
    protected UnitTester $tester;
    public function _before(): void
    {
        $this->tester->haveFixtures(
            [
                'user' => [
                    'class' => UserFixture::class,
                    'dataFile' => codecept_data_dir() . 'user.php',
                ],
            ],
        );
    }

    public function testCorrectSignup(): void
    {
        $model = new SignupForm(
            [
                'username' => 'some_username',
                'email' => 'some_email@example.com',
                'password' => 'some_password',
                'passwordConfirm' => 'some_password',
            ],
        );

        $user = $model->signup(Yii::$app->mailer, Yii::$app->params['supportEmail'], Yii::$app->name);

        verify($user)->notEmpty();

        /** @var User $user */
        $user = $this->tester->grabRecord(
            User::class, 
            [
                'username' => 'some_username',
                'email' => 'some_email@example.com',
                'status' => User::STATUS_INACTIVE,
            ],
        );

        $this->tester->seeEmailIsSent();
        $mail = $this->tester->grabLastSentEmail();

        verify($mail)
            ->instanceOf(MessageInterface::class);
        verify($mail->getTo())
            ->arrayHasKey('some_email@example.com');
        verify($mail->getFrom())
            ->arrayHasKey(Yii::$app->params['supportEmail']);
        verify($mail->getSubject())
            ->equals('Account registration at ' . Yii::$app->name);
        verify($mail->toString())
            ->stringContainsString($user->verification_token);
    }

    public function testNotCorrectSignup(): void
    {
        $model = new SignupForm(
            [
                'username' => 'troy.becker',
                'email' => 'nicolas.dianna@hotmail.com',
                'password' => 'some_password',
            ],
        );

        verify($model->signup(Yii::$app->mailer, Yii::$app->params['supportEmail'], Yii::$app->name))
            ->empty();
        verify($model->getErrors('username'))
            ->notEmpty();
        verify($model->getErrors('email'))
            ->notEmpty();
        verify($model->getFirstError('username'))
            ->equals('This username has already been taken.');
        verify($model->getFirstError('email'))
            ->equals('This email address has already been taken.');
    }

    private const DIRECTORY = [
        'EMP11727 - ekaibei@kemri.go.ke - Edwin Kaibei Test' => 'EMP11727 - Edwin Kaibei Test - No Dept. - ',
        '90296 - jlangat@kemri.go.ke - DR.JOAN CHERONO LANGAT' => '90296 - DR.JOAN CHERONO LANGAT - SSRC - DIR005',
    ];

    private function formWithDirectory(array $config, array $directory = self::DIRECTORY): SignupForm
    {
        $form = new class extends SignupForm {
            public array $directory = [];

            public function fetchAssignees()
            {
                return $this->directory;
            }
        };
        $form->directory = $directory;
        Yii::configure($form, $config);

        return $form;
    }

    public function testParseAssigneeWithDepartment(): void
    {
        $employee = SignupForm::parseAssignee(
            '90296 - jlangat@kemri.go.ke - DR.JOAN CHERONO LANGAT',
            '90296 - DR.JOAN CHERONO LANGAT - SSRC - DIR005',
        );

        verify($employee['staffID'])->equals('90296');
        verify($employee['email'])->equals('jlangat@kemri.go.ke');
        verify($employee['fullNames'])->equals('DR.JOAN CHERONO LANGAT');
        verify($employee['department'])->equals('SSRC');
        verify($employee['departmentCode'])->equals('DIR005');
    }

    public function testParseAssigneeWithoutDepartment(): void
    {
        $employee = SignupForm::parseAssignee(
            'EMP11727 - ekaibei@kemri.go.ke - Edwin Kaibei Test',
            'EMP11727 - Edwin Kaibei Test - No Dept. - ',
        );

        verify($employee['staffID'])->equals('EMP11727');
        verify($employee['department'])->null();
        verify($employee['departmentCode'])->null();
    }

    public function testParseAssigneeKeepsHyphenatedEmail(): void
    {
        $employee = SignupForm::parseAssignee(
            '90001 - mary-ann.otieno@kemri.go.ke - MARY-ANN OTIENO',
            '90001 - MARY-ANN OTIENO - SSRC - DIR005',
        );

        verify($employee['email'])->equals('mary-ann.otieno@kemri.go.ke');
        verify($employee['fullNames'])->equals('MARY-ANN OTIENO');
        verify($employee['department'])->equals('SSRC');
    }

    public function testParseAssigneeRejectsMalformedKey(): void
    {
        verify(SignupForm::parseAssignee('status', '500'))->null();
    }

    public function testSignupPersistsEmployeeDetails(): void
    {
        $model = $this->formWithDirectory([
            'staffID' => ' 90296 ',
            'username' => 'jlangat',
            'email' => 'jlangat@kemri.go.ke',
            'password' => 'some_password',
            'passwordConfirm' => 'some_password',
        ]);

        verify($model->signup(Yii::$app->mailer, Yii::$app->params['supportEmail'], Yii::$app->name))->true();

        $this->tester->seeRecord(User::class, [
            'username' => 'jlangat',
            'staff_id' => '90296',
            'department' => 'SSRC',
            'department_code' => 'DIR005',
        ]);
    }

    public function testSignupStoresNullDepartmentWhenDirectoryHasNone(): void
    {
        $model = $this->formWithDirectory([
            'staffID' => 'EMP11727',
            'username' => 'ekaibei',
            'email' => 'ekaibei@kemri.go.ke',
            'password' => 'some_password',
            'passwordConfirm' => 'some_password',
        ]);

        verify($model->signup(Yii::$app->mailer, Yii::$app->params['supportEmail'], Yii::$app->name))->true();

        $this->tester->seeRecord(User::class, [
            'username' => 'ekaibei',
            'staff_id' => 'EMP11727',
            'department' => null,
            'department_code' => null,
        ]);
    }

    public function testSignupIgnoresStaffIdNotInDirectory(): void
    {
        $model = $this->formWithDirectory([
            'staffID' => '00000',
            'username' => 'unknown_staff',
            'email' => 'unknown_staff@example.com',
            'password' => 'some_password',
            'passwordConfirm' => 'some_password',
        ]);

        verify($model->signup(Yii::$app->mailer, Yii::$app->params['supportEmail'], Yii::$app->name))->true();

        $this->tester->seeRecord(User::class, ['username' => 'unknown_staff', 'staff_id' => null]);
    }

    public function testSignupRejectsDuplicateStaffId(): void
    {
        $first = $this->formWithDirectory([
            'staffID' => '90296',
            'username' => 'jlangat',
            'email' => 'jlangat@kemri.go.ke',
            'password' => 'some_password',
            'passwordConfirm' => 'some_password',
        ]);
        verify($first->signup(Yii::$app->mailer, Yii::$app->params['supportEmail'], Yii::$app->name))->true();

        $second = $this->formWithDirectory([
            'staffID' => '90296',
            'username' => 'jlangat2',
            'email' => 'jlangat2@kemri.go.ke',
            'password' => 'some_password',
            'passwordConfirm' => 'some_password',
        ]);

        verify($second->signup(Yii::$app->mailer, Yii::$app->params['supportEmail'], Yii::$app->name))->empty();
        verify($second->getFirstError('staffID'))->equals('An account already exists for this employee number.');
    }
}
