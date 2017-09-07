<?php

/**
 * AuthCest Class.
 * 
 * @author Johan Alvarez <llstarscreamll@hotmail.com>
 */
class AuthCest
{
    private $testToken = '2661EEC13EAE8F88A807FB2478BE4A7F0EE8E49595BB5E9C501007518152CAB9411FCAC6D29139454A344BEC09915F3F621B706E1F593D9B8FDF29E94A74E19B6C7C346E168892E05DFDC62FD1CABD2455533A2F71C5B5AC348EE08A02C09036C3E0B34C656C0F4CEA1FCDC69D1D4DA5CDE5EC770BCDAD1175112445A567494587F11DCF1818E976293D9DC99F4E2E154C80648DED385836779F7BDFCC8C225CB24C55A665770EA2916352FCED5D97E1DDE378A83281D8DA49F1A565EE64FE76C212520B91D6FD62559EF36F4F299722';

    /**
     * Auth success test.
     */
    public function shouldAuthSuccess(ApiTester $I)
    {
        $I->sendPost('api/decrypt', [
            'key' => $this->testToken
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['data' => 'Welcome Pruebas_CSTN']);
    }

    /**
     * Should redirect if there are not token data.
     */
    public function showErrorPageIfThereIsNoKeyDataOnRequest(ApiTester $I)
    {
        $errorMsg = 'No se han recivido datos de token.';

        $I->sendPost('api/decrypt', []); // send post with no token data

        // grab current URL
        $currentURL = $I->getApplication()->request->fullUrl();

        // should 
        $I->seeCurrentUrlEquals('/error?error='.bin2Hex($errorMsg));
    }

    /**
     * If error is received, should redirect with error var on URL and converted to Hex. 
     */
    public function shouldShowTheGivenErrorsOnPage(ApiTester $I)
    {
        $errorMsg = 'User email not found';

        // send POST with error and without token data
        $I->sendPost('api/decrypt', [
            'error' => $errorMsg
        ]);

        // nothing to do here, the app should redirect to error page
        $I->seeInCurrentUrl('/error?error='.bin2hex($errorMsg));
    }
}
