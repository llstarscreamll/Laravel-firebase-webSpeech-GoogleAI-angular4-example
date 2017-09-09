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
                'resolvedQuery' => 'crear solicitud compra de tablets',
                'action' => 'action.create-request',
                'actionIncomplete' => false,
                "parameters" => [
                    "request-name" => "compra de tablets"
                ],
                'metadata' => [
                  'intentName' => 'crear-solicitud'
                ]
            ],
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'speech' => 'qué artículos añado a solicitud "compra de tablets"?',
        ]);
        $I->seeResponseJsonMatchesJsonPath('$.speech');
        $I->seeResponseJsonMatchesJsonPath('$.displayText');
        $I->seeResponseJsonMatchesJsonPath('$.data.request_id');
    }
/*
    public function onlySuggestItemsByNameMatchs(ApiTester $I)
    {
        $speechRequestName = 'speech request testing';
        $speechRequest = $this->requestService->createByName($speechRequestName);
        $itemOne = $this->itemsService->createByName('xx bar xx');
        $itemTwo = $this->itemsService->createByName('foo item 1');

        $I->sendPost('api/ai', [
            'result' => [
                'resolvedQuery' => 'xx bar:'.$speechRequest->getKey(),
                'parameters' => [],
                'metadata' => [
                  'intentName' => 'buscar-articulo',
                ]
            ],
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['speech' => "Se encontraron 1 artículos, cual eliges?"]);
        $this->itemsService->deleteById($itemOne->getKey());
        $this->itemsService->deleteById($itemTwo->getKey());
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
        $this->itemsService->deleteById($item->getKey());
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
*/
}
