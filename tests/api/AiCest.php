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
        
        $this->requestService->deleteAll();
        $this->itemsService->deleteAll();
    }

    public function _after(ApiTester $I)
    {
        $this->requestService->deleteAll();
        $this->itemsService->deleteAll();
    }

    public function createSpeechRequestWithGivenName(ApiTester $I)
    {
        $I->sendPost('api/ai', [
            'result' => [
                'resolvedQuery' => 'crear solicitud compra de tablets',
                'action' => 'action.create-request',
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
        $I->seeResponseJsonMatchesJsonPath('$.contextOut[0].name');
        $I->seeResponseJsonMatchesJsonPath('$.contextOut[0].lifespan');
        $I->seeResponseJsonMatchesJsonPath('$.contextOut[0].parameters.request_id');
    }

    public function onlySuggestItemsByMatchedNames(ApiTester $I)
    {
        $speechRequestName = 'speech request testing';
        $speechRequest = $this->requestService->createByName($speechRequestName);
        $itemOne = $this->itemsService->createByName('xx bar xx');
        $itemTwo = $this->itemsService->createByName('licencias de software IBM');

        $I->sendPost('api/ai', [
            'result' => [
                'resolvedQuery' => 'agregar 4 licencias de software',
                'action' => 'action.add-item-to-request',
                'parameters' => [
                    "item-name" => "licencias de software",
                    "item-quantity" => "4",
                    "request_id" => "-Ktb5o3-3Ss5_2jGSAiE",
                ],
                'metadata' => [
                  'intentName' => 'agregar-articulo',
                ]
            ],
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['speech' => "He añadido 4 licencias de software, algo mas?"]);
        $this->itemsService->deleteById($itemOne->getKey());
        $this->itemsService->deleteById($itemTwo->getKey());
    }

    public function addItemsSuggestionsToGivenSpeechRequest(ApiTester $I)
    {
        $speechRequestName = 'speech request testing';
        $speechRequest = $this->requestService->createByName($speechRequestName);
        $item = $this->itemsService->createByName('foo item 1');
        $item2 = $this->itemsService->createByName('foo item 2');
        $items = count($this->itemsService->searchByName('foo item'));

        $I->sendPost('api/ai', [
            'result' => [
                'resolvedQuery' => 'agregar 4 foo item',
                'action' => 'action.add-item-to-request',
                'parameters' => [
                    "item-name" => "foo item",
                    "item-quantity" => "4",
                    "request_id" => "-Ktb5o3-3Ss5_2jGSAiE",
                ],
                'metadata' => [
                  'intentName' => 'agregar-articulo',
                ]
            ],
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['speech' => "He encontrado {$items} coincidencias de foo item, por favor sé mas específico..."]);
        $this->itemsService->deleteById($item->getKey());
        $this->itemsService->deleteById($item2->getKey());
    }

    public function handleNonFoundSuggestionsToSpeechRequest(ApiTester $I)
    {
        $speechRequestName = 'speech request testing';
        $speechRequest = $this->requestService->createByName($speechRequestName);

        $I->sendPost('api/ai', [
            'result' => [
                'resolvedQuery' => 'agregar 4 foo item',
                'action' => 'action.add-item-to-request',
                'parameters' => [
                    "item-name" => "foo item",
                    "item-quantity" => "4",
                    "request_id" => "-Ktb5o3-3Ss5_2jGSAiE",
                ],
                'metadata' => [
                  'intentName' => 'agregar-articulo',
                ]
            ],
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['speech' => "No encontré coincidencias del artículo foo item"]);
    }

}
