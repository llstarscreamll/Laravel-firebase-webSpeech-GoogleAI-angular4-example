<?php

/**
 * AiCest Class.
 * 
 * @author Johan Alvarez <llstarscreamll@hotmail.com>
 */
class AiCest
{
    public function aiRequestName(ApiTester $I)
    {
        $I->sendPost('api/ai', [
            'result' => [
                'resolvedQuery' => 'compra de tablets para dpto calidad',
                'action' => 'action.request-name',
                'actionIncomplete' => false,
                'parameters' => [],
                'metadata' => [
                  'intentName' => 'nombre-solicitud'
                ]
            ],
        ]);

        $I->seeResponseCodeIs(200);
        // TODO: add json path matches
        // $I->seeResponseContainsJson(['speech' => 'He creado tu solicitud']);
    }
}
