<?php
namespace api\tests;

use api\tests\ApiTester;

class UserAuthSigninCest
{
    /**
     * Check bad method
     * @param \api\tests\ApiTester $I
     */
    public function badMethod(ApiTester $I) {
        $I->sendGET('/user/auth/signin');
        $I->seeResponseCodeIs(405);
        $I->seeResponseIsJson();
    }

    /**
     * Check bad method
     * @param \api\tests\ApiTester $I
     */
    public function emptyParams(ApiTester $I) {
        $I->sendPOST('/user/auth/signin');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function emptyEmail(ApiTester $I) {
        $I->sendPOST('/user/auth/signin', [
            'password' => 'wrong-password',
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'name' => 'ERROR_USER_FIELD_EMPTY_EMAIL',
        ]);
    }

    public function emptyPassword(ApiTester $I) {
        $I->sendPOST('/user/auth/signin', [
            'email' => 'test@sprut.ai',
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'name' => 'ERROR_USER_FIELD_EMPTY_PASSWORD',
        ]);
    }

    public function failed(ApiTester $I) {
        $I->sendPOST('/user/auth/signin', [
            'email' => 'test',
            'password' => 'test'
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'name' => 'ERROR_USER_FIELD_INVALID_PASSWORD',
        ]);
    }

    public function success(ApiTester $I) {
        $I->sendPOST('/user/auth/signin', [
            'email' => 'safronov.ser@icloud.com',
            'password' => 'uD4xmj5890'
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'user' => [
                'email' => 'safronov.ser@icloud.com'
            ],
        ]);
    }
}
