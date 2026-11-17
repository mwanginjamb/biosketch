<?php

declare(strict_types=1);

namespace frontend\tests\Functional;

use common\components\RbacHelper;
use common\models\User;
use common\models\UserApiToken;
use frontend\models\Researcher;
use frontend\models\ResearcherEducation;
use frontend\tests\Support\FunctionalTester;
use Yii;

/**
 * REST API v1: authentication, ownership and nested-resource scoping.
 */
final class ApiCest
{
    private const BASE = '/index-test.php/apiv1';

    private int $ownerId;
    private int $otherId;
    private int $ownProfileId;
    private int $otherActiveProfileId;
    private int $otherDraftProfileId;
    private string $ownerToken;
    private string $otherToken;

    public function _before(FunctionalTester $I): void
    {
        // Token endpoint is throttled per IP; reset so repeated runs don't hit 429.
        foreach (['', '127.0.0.1', '::1'] as $ip) {
            Yii::$app->cache->delete(['throttle', 'api-token', $ip]);
        }

        $this->ownerId = $this->haveUser($I, 'api_owner');
        $this->otherId = $this->haveUser($I, 'api_other');
        $I->haveRecord(User::class, [
            'username' => 'api_inactive',
            'email' => 'api_inactive@example.com',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('password_0'),
            'status' => User::STATUS_INACTIVE,
        ]);

        $this->ownProfileId = $I->haveRecord(Researcher::class, [
            'user_id' => $this->ownerId, 'full_name' => 'Owner Draft', 'status' => 0,
        ]);
        $this->otherActiveProfileId = $I->haveRecord(Researcher::class, [
            'user_id' => $this->otherId, 'full_name' => 'Other Active', 'status' => 1,
        ]);
        $otherSecondUser = $this->haveUser($I, 'api_other2');
        $this->otherDraftProfileId = $I->haveRecord(Researcher::class, [
            'user_id' => $otherSecondUser, 'full_name' => 'Other Draft', 'status' => 0,
        ]);

        $this->ownerToken = UserApiToken::issue(User::findOne($this->ownerId))[1];
        $this->otherToken = UserApiToken::issue(User::findOne($this->otherId))[1];
    }

    /** Creates an active account with the default `researcher` role, as signup does. */
    private function haveUser(FunctionalTester $I, string $username): int
    {
        $id = $I->haveRecord(User::class, [
            'username' => $username,
            'email' => $username . '@example.com',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('password_0'),
            'status' => User::STATUS_ACTIVE,
        ]);
        RbacHelper::assignDefaultRole($id);

        return $id;
    }

    private function json(FunctionalTester $I): mixed
    {
        return json_decode($I->grabPageSource(), true);
    }

    private function as(FunctionalTester $I, string $token): void
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
    }

    // ---- authentication -------------------------------------------------

    public function requestsWithoutTokenAreRejected(FunctionalTester $I): void
    {
        $I->sendAjaxGetRequest(self::BASE . '/researchers');
        $I->seeResponseCodeIs(401);
        verify($this->json($I))->arrayHasKey('message');
    }

    public function invalidTokenIsRejected(FunctionalTester $I): void
    {
        $this->as($I, 'not-a-real-token');
        $I->sendAjaxGetRequest(self::BASE . '/researchers');
        $I->seeResponseCodeIs(401);
    }

    public function tokenIsIssuedForValidCredentials(FunctionalTester $I): void
    {
        $I->sendAjaxPostRequest(self::BASE . '/auth/token', ['username' => 'api_owner', 'password' => 'password_0']);
        $I->seeResponseCodeIs(201);
        $body = $this->json($I);
        verify($body['token_type'])->equals('Bearer');
        verify(strlen($body['token']))->equals(48);

        // Only the hash is stored.
        $I->seeRecord(UserApiToken::class, ['token_hash' => UserApiToken::hash($body['token'])]);
        $I->dontSeeRecord(UserApiToken::class, ['token_hash' => $body['token']]);

        $this->as($I, $body['token']);
        $I->sendAjaxGetRequest(self::BASE . '/researchers');
        $I->seeResponseCodeIs(200);
    }

    public function tokenIsRefusedForBadPasswordOrInactiveUser(FunctionalTester $I): void
    {
        $I->sendAjaxPostRequest(self::BASE . '/auth/token', ['username' => 'api_owner', 'password' => 'wrong']);
        $I->seeResponseCodeIs(401);

        $I->sendAjaxPostRequest(self::BASE . '/auth/token', ['username' => 'api_inactive', 'password' => 'password_0']);
        $I->seeResponseCodeIs(401);
    }

    public function expiredTokenIsRejected(FunctionalTester $I): void
    {
        [$token, $plain] = UserApiToken::issue(User::findOne($this->ownerId));
        $token->updateAttributes(['expires_at' => time() - 1]);

        $this->as($I, $plain);
        $I->sendAjaxGetRequest(self::BASE . '/researchers');
        $I->seeResponseCodeIs(401);
    }

    public function revokedTokenStopsWorking(FunctionalTester $I): void
    {
        $this->as($I, $this->ownerToken);
        $I->sendAjaxRequest('DELETE', self::BASE . '/auth/token');
        $I->seeResponseCodeIs(204);

        $I->sendAjaxGetRequest(self::BASE . '/researchers');
        $I->seeResponseCodeIs(401);
    }

    // ---- researchers ----------------------------------------------------

    public function indexListsOnlyActiveProfiles(FunctionalTester $I): void
    {
        $this->as($I, $this->ownerToken);
        $I->sendAjaxGetRequest(self::BASE . '/researchers');
        $I->seeResponseCodeIs(200);

        $names = array_column($this->json($I)['items'], 'full_name');
        verify($names)->equals(['Other Active']);
        verify($this->json($I)['items'][0])->arrayHasKey('staff_id');
        verify($this->json($I)['items'][0])->arrayHasNotKey('created_by');
    }

    // ---- lookup by username -----------------------------------------------

    public function byUsernameReturnsTheSameResourceAsById(FunctionalTester $I): void
    {
        $this->as($I, $this->ownerToken);

        $I->sendAjaxGetRequest(self::BASE . '/researchers/' . $this->otherActiveProfileId);
        $I->seeResponseCodeIs(200);
        $byId = $this->json($I);

        $I->sendAjaxGetRequest(self::BASE . '/researchers/by-username/api_other');
        $I->seeResponseCodeIs(200);
        verify($this->json($I))->equals($byId);

        // ?expand= works the same way.
        $I->sendAjaxGetRequest(self::BASE . '/researchers/by-username/api_other?expand=education,statement');
        $I->seeResponseCodeIs(200);
        verify($this->json($I))->arrayHasKey('education');
        verify($this->json($I))->arrayHasKey('statement');
    }

    public function byUsernameFollowsDraftVisibility(FunctionalTester $I): void
    {
        $this->as($I, $this->ownerToken);

        // Own draft: visible. Someone else's draft: hidden as 404.
        $I->sendAjaxGetRequest(self::BASE . '/researchers/by-username/api_owner');
        $I->seeResponseCodeIs(200);
        verify($this->json($I)['id'])->equals($this->ownProfileId);

        $I->sendAjaxGetRequest(self::BASE . '/researchers/by-username/api_other2');
        $I->seeResponseCodeIs(404);
    }

    public function byUsernameNotFoundAndAuth(FunctionalTester $I): void
    {
        $I->sendAjaxGetRequest(self::BASE . '/researchers/by-username/api_other');
        $I->seeResponseCodeIs(401);   // token required

        $this->as($I, $this->ownerToken);
        $I->sendAjaxGetRequest(self::BASE . '/researchers/by-username/no.such.user');
        $I->seeResponseCodeIs(404);

        $this->haveUser($I, 'api_noprofile');   // account without a biosketch
        $I->sendAjaxGetRequest(self::BASE . '/researchers/by-username/api_noprofile');
        $I->seeResponseCodeIs(404);
    }

    public function byUsernameWorksForAllDigitUsernames(FunctionalTester $I): void
    {
        // An all-digit username must not be confused with a numeric {id}.
        $numericUserId = $this->haveUser($I, '90296');
        $profileId = $I->haveRecord(Researcher::class, [
            'user_id' => $numericUserId, 'full_name' => 'Numeric Username', 'status' => 1,
        ]);

        $this->as($I, $this->ownerToken);
        $I->sendAjaxGetRequest(self::BASE . '/researchers/by-username/90296');
        $I->seeResponseCodeIs(200);
        verify($this->json($I)['id'])->equals($profileId);

        // GET /researchers/{id} is unchanged.
        $I->sendAjaxGetRequest(self::BASE . '/researchers/' . $profileId);
        $I->seeResponseCodeIs(200);
        verify($this->json($I)['full_name'])->equals('Numeric Username');
    }

    public function draftsAreVisibleOnlyToTheirOwner(FunctionalTester $I): void
    {
        $this->as($I, $this->ownerToken);

        $I->sendAjaxGetRequest(self::BASE . '/researchers/' . $this->ownProfileId);
        $I->seeResponseCodeIs(200);

        $I->sendAjaxGetRequest(self::BASE . '/researchers/' . $this->otherDraftProfileId);
        $I->seeResponseCodeIs(404);

        $I->sendAjaxGetRequest(self::BASE . '/researchers/' . $this->otherActiveProfileId);
        $I->seeResponseCodeIs(200);
    }

    public function createUsesTheCallerAsOwnerAndAllowsOneProfile(FunctionalTester $I): void
    {
        $newUserId = $this->haveUser($I, 'api_new');
        $this->as($I, UserApiToken::issue(User::findOne($newUserId))[1]);

        $I->sendAjaxPostRequest(self::BASE . '/researchers', [
            'full_name' => 'New Researcher',
            'user_id' => $this->otherId,   // must be ignored
        ]);
        $I->seeResponseCodeIs(201);
        $I->seeRecord(Researcher::class, ['full_name' => 'New Researcher', 'user_id' => $newUserId]);

        $I->sendAjaxPostRequest(self::BASE . '/researchers', ['full_name' => 'Second Profile']);
        $I->seeResponseCodeIs(409);
    }

    public function onlyTheOwnerCanUpdateOrDelete(FunctionalTester $I): void
    {
        $this->as($I, $this->ownerToken);

        $I->sendAjaxRequest('PUT', self::BASE . '/researchers/' . $this->otherActiveProfileId, ['full_name' => 'Hacked']);
        $I->seeResponseCodeIs(403);
        $I->sendAjaxRequest('DELETE', self::BASE . '/researchers/' . $this->otherActiveProfileId);
        $I->seeResponseCodeIs(403);
        $I->seeRecord(Researcher::class, ['id' => $this->otherActiveProfileId, 'full_name' => 'Other Active']);

        $I->sendAjaxRequest('PUT', self::BASE . '/researchers/' . $this->ownProfileId, [
            'full_name' => 'Owner Renamed',
            'user_id' => $this->otherId,   // must be ignored
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeRecord(Researcher::class, [
            'id' => $this->ownProfileId, 'full_name' => 'Owner Renamed', 'user_id' => $this->ownerId,
        ]);
    }

    public function photoUploadRequiresOwnership(FunctionalTester $I): void
    {
        $this->as($I, $this->ownerToken);
        $I->sendAjaxPostRequest(self::BASE . '/researchers/' . $this->otherActiveProfileId . '/photo');
        $I->seeResponseCodeIs(403);

        $I->sendAjaxPostRequest(self::BASE . '/researchers/' . $this->ownProfileId . '/photo');
        $I->seeResponseCodeIs(400);   // no file sent
    }

    // ---- nested resources -----------------------------------------------

    public function everyNestedResourceIsRouted(FunctionalTester $I): void
    {
        $this->as($I, $this->ownerToken);
        foreach (['educations', 'identifiers', 'media', 'grants', 'publications'] as $resource) {
            $I->sendAjaxGetRequest(self::BASE . '/researchers/' . $this->ownProfileId . '/' . $resource);
            $I->seeResponseCodeIs(200);
            verify($this->json($I))->arrayHasKey('items');
        }
    }

    public function drdCanReadDraftsButNotWrite(FunctionalTester $I): void
    {
        $drdId = $I->haveRecord(User::class, [
            'username' => 'api_drd',
            'email' => 'api_drd@example.com',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('password_0'),
            'status' => User::STATUS_ACTIVE,
        ]);
        RbacHelper::assign(RbacHelper::ROLE_DRD, $drdId);
        $this->as($I, UserApiToken::issue(User::findOne($drdId))[1]);

        $I->sendAjaxGetRequest(self::BASE . '/researchers/' . $this->otherDraftProfileId);
        $I->seeResponseCodeIs(200);
        $I->sendAjaxGetRequest(self::BASE . '/researchers/' . $this->otherDraftProfileId . '/educations');
        $I->seeResponseCodeIs(200);

        $I->sendAjaxRequest('PATCH', self::BASE . '/researchers/' . $this->otherDraftProfileId, ['full_name' => 'X']);
        $I->seeResponseCodeIs(403);
        $I->sendAjaxPostRequest(self::BASE . '/researchers', ['full_name' => 'DRD Profile']);
        $I->seeResponseCodeIs(403);   // drd alone has no createBiosketch
    }

    public function apiIndexIsPublic(FunctionalTester $I): void
    {
        $I->sendAjaxGetRequest(self::BASE);
        $I->seeResponseCodeIs(200);
        verify($this->json($I)['version'])->equals('v1');
    }

    public function ownerCanManageNestedRecords(FunctionalTester $I): void
    {
        $this->as($I, $this->ownerToken);
        $url = self::BASE . '/researchers/' . $this->ownProfileId . '/educations';

        $I->sendAjaxPostRequest($url, [
            'degree' => 'PhD',
            'institution_name' => 'University of Nairobi',
            'researcher_id' => $this->otherActiveProfileId,   // must be ignored
        ]);
        $I->seeResponseCodeIs(201);
        $id = $this->json($I)['id'];
        $I->seeRecord(ResearcherEducation::class, ['id' => $id, 'researcher_id' => $this->ownProfileId]);

        // Cannot move it to another researcher via update.
        $I->sendAjaxRequest('PATCH', $url . '/' . $id, [
            'degree' => 'MSc',
            'researcher_id' => $this->otherActiveProfileId,
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeRecord(ResearcherEducation::class, ['id' => $id, 'researcher_id' => $this->ownProfileId, 'degree' => 'MSc']);

        $I->sendAjaxGetRequest($url);
        $I->seeResponseCodeIs(200);
        verify(array_column($this->json($I)['items'], 'id'))->equals([$id]);

        $I->sendAjaxRequest('DELETE', $url . '/' . $id);
        $I->seeResponseCodeIs(204);
        $I->dontSeeRecord(ResearcherEducation::class, ['id' => $id]);
    }

    public function nestedRecordsOfOthersAreReadOnlyOrHidden(FunctionalTester $I): void
    {
        $eduId = $I->haveRecord(ResearcherEducation::class, [
            'researcher_id' => $this->otherActiveProfileId, 'degree' => 'BSc', 'institution_name' => 'KU',
        ]);
        $this->as($I, $this->ownerToken);
        $otherUrl = self::BASE . '/researchers/' . $this->otherActiveProfileId . '/educations';

        // Active profile: readable, not writable.
        $I->sendAjaxGetRequest($otherUrl . '/' . $eduId);
        $I->seeResponseCodeIs(200);
        $I->sendAjaxPostRequest($otherUrl, ['degree' => 'X', 'institution_name' => 'Y']);
        $I->seeResponseCodeIs(403);
        $I->sendAjaxRequest('DELETE', $otherUrl . '/' . $eduId);
        $I->seeResponseCodeIs(403);

        // Record looked up under the wrong researcher.
        $I->sendAjaxGetRequest(self::BASE . '/researchers/' . $this->ownProfileId . '/educations/' . $eduId);
        $I->seeResponseCodeIs(404);

        // Someone else's draft profile: hidden entirely.
        $I->sendAjaxGetRequest(self::BASE . '/researchers/' . $this->otherDraftProfileId . '/educations');
        $I->seeResponseCodeIs(404);
    }
}
