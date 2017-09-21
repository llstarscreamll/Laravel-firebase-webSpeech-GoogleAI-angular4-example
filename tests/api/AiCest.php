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

    public function approveSpecificRequestFromGivenList(ApiTester $I)
    {
        $speechRequest1 = $this->requestService->createByName('Foo request 01');
        $speechRequest2 = $this->requestService->createByName('Foo request 11');
        $speechRequest3 = $this->requestService->createByName('Foo request 22');

        $I->sendPost('api/ai', [
            'result' => [
                'resolvedQuery' => 'aprobar la segunda',
                'action' => 'action.select-request-to-approve',
                'parameters' => [
                    "request-name" => "Foo request",
                    "selected" => "2"
                ],
                'metadata' => [
                  'intentName' => 'seleccionar-solicitud-a-aprobar',
                ]
            ],
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['speech' => "La solicitud \"Foo request 22\" fue aprovada correctamente."]);
    }

    public function approveSingleRequestFound(ApiTester $I)
    {
        $speechRequest = $this->requestService->createByName('Foo request');

        $I->sendPost('api/ai', [
            'result' => [
                'resolvedQuery' => 'aprobar solicitud Foo',
                'action' => 'action.approve-request',
                'parameters' => [
                    "request-name" => "Foo request",
                ],
                'metadata' => [
                  'intentName' => 'aprobar-solicitud',
                ]
            ],
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['speech' => "Solicitud \"Foo request\" aprovada correctamente. Ya no te ayudo mas... Ve y llena los formularios."]);
    }

    public function handleNotFoundRequestToApprove(ApiTester $I)
    {
        $I->sendPost('api/ai', [
            'result' => [
                'resolvedQuery' => 'aprobar solicitud Foo',
                'action' => 'action.approve-request',
                'parameters' => [
                    "request-name" => "Foo",
                ],
                'metadata' => [
                  'intentName' => 'aprobar-solicitud',
                ]
            ],
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['speech' => "No encontré solicitud con ese nombre, intenta otro."]);
    }

    public function searchRequestsToApprove(ApiTester $I)
    {
        $speechRequest1 = $this->requestService->createByName('Foo 1');
        $speechRequest2 = $this->requestService->createByName('Foo 2');

        $I->sendPost('api/ai', [
            'result' => [
                'resolvedQuery' => 'aprobar solicitud Foo',
                'action' => 'action.approve-request',
                'parameters' => [
                    "request-name" => "Foo",
                ],
                'metadata' => [
                  'intentName' => 'aprobar-solicitud',
                ]
            ],
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['speech' => "Encontré 2 coincidencias. ¿Cual deseas aprobar?"]);
        $I->seeResponseContainsJson(['data' => [
            'matches' => [
                0 => ['key' => $speechRequest1->getKey()] + $speechRequest1->getValue(),
                1 => ['key' => $speechRequest2->getKey()] + $speechRequest2->getValue(),
            ],
        ]]);
    }

    public function finishRequest(ApiTester $I)
    {
        $speechRequestName = 'speech request testing';
        $speechRequest = $this->requestService->createByName($speechRequestName);

        $I->sendPost('api/ai', [
            'result' => [
                'resolvedQuery' => 'finalizar solicitud',
                'action' => 'action.finish-request',
                'parameters' => [
                    "request_id" => "-Ktb5o3-3Ss5_2jGSAiE",
                ],
                'metadata' => [
                  'intentName' => 'finializar-solicitud',
                ]
            ],
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['speech' => "Solicitud finalizada, ahora está pendiente de aprobación. Fue un gusto ayudarte."]);
    }

    public function cancelRequest(ApiTester $I)
    {
        $speechRequestName = 'speech request testing';
        $speechRequest = $this->requestService->createByName($speechRequestName);

        $I->sendPost('api/ai', [
            'result' => [
                'resolvedQuery' => 'cancelar solicitud',
                'action' => 'action.cancel-request',
                'parameters' => [
                    "request_id" => "-Ktb5o3-3Ss5_2jGSAiE",
                ],
                'metadata' => [
                  'intentName' => 'cancelar-solicitud',
                ]
            ],
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['speech' => "Se ha cancelado tu solicitud. Fue un gusto asistirte."]);
    }

    public function fixItemsSuggestionCollision(ApiTester $I)
    {
        $speechRequestName = 'speech request testing';
        $speechRequest = $this->requestService->createByName($speechRequestName);
        $itemOne = $this->itemsService->createByName('computador');
        $itemTwo = $this->itemsService->createByName('computador Toshiba');

        $I->sendPost('api/ai', [
            'result' => [
                'resolvedQuery' => 'agregar 4 computador',
                'action' => 'action.add-item-to-request',
                'parameters' => [
                    "item-name" => "computador",
                    "item-quantity" => "4",
                    "request_id" => "-Ktb5o3-3Ss5_2jGSAiE",
                ],
                'metadata' => [
                  'intentName' => 'agregar-articulo',
                ]
            ],
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['speech' => "He añadido 4 computador. ¿Algo mas?"]);
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
            'speech' => '¿Qué artículos añado a solicitud "compra de tablets"?',
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
        $I->seeResponseContainsJson(['speech' => "He añadido 4 licencias de software. ¿Algo mas?"]);
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
        $I->seeResponseContainsJson(['speech' => "He encontrado {$items} coincidencias de foo item. Por favor sé mas específico..."]);
        // $this->itemsService->deleteById($item->getKey());
        // $this->itemsService->deleteById($item2->getKey());
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
