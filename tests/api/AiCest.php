<?php

use App\Services\ItemsService;
use App\Services\RequestService;

/**
 * AiCest Class.
 * 
 * @author Johan Alvarez <llstarscreamll@hotmail.com>
 */
class AiCest
{
    public function _before(ApiTester $I)
    {
        $this->itemsService = app(ItemsService::class);
        $this->requestService = app(RequestService::class);
    }

    public function _after(ApiTester $I)
    {
        $this->requestService->deleteAll();
    }

    public function createSpeechRequestWithGivenName(ApiTester $I)
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
    }

    public function addItemsSuggestionsToGivenSpeechRequest(ApiTester $I)
    {
        $speechRequestName = 'speech request testing';
        $speechRequest = $this->requestService->createByName($speechRequestName);
        $item = $this->itemsService->createByName('foo item 1');
        $items = count($this->itemsService->searchByName('foo item 1'));

        $I->sendPost('api/ai', [
            'result' => [
                'resolvedQuery' => 'foo item 1:'.$speechRequest->getKey(),
                'parameters' => [],
                'metadata' => [
                  'intentName' => 'buscar-articulo',
                ]
            ],
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['speech' => "Se encontraron $items artículos, cual eliges?"]);
    }

    public function handleNonFoundSuggestionsToSpeechRequest(ApiTester $I)
    {
        $speechRequestName = 'speech request testing';
        $speechRequest = $this->requestService->createByName($speechRequestName);

        $I->sendPost('api/ai', [
            'result' => [
                'resolvedQuery' => 'xx bla bla bla xx:'.$speechRequest->getKey(),
                'parameters' => [],
                'metadata' => [
                  'intentName' => 'buscar-articulo',
                ]
            ],
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['speech' => "No se encontraron sugerencias..."]);
    }
}
