<?php

use Illuminate\Http\Request;
use App\Services\ItemsService;
use App\Services\RequestService;
use App\Services\FirebaseService;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/decrypt', 'AuthController@login');

Route::post('/ai', function(Request $request) {

	$data = $request->all();
	$requestService = app(RequestService::class);
	$itemsService = app(ItemsService::class);
	$intent = $data['result']['metadata']['intentName'];
	$action = $data['result']['action'];
	$parameters = $data['result']['parameters'];
	$msg = $data['result']['resolvedQuery'];
	$speech = "Opps!! Algo ha salido mal..";
	$data = [];
	$contextOut = [];

	switch ($action) {

		case 'action.add-item-to-request':
			// the speech request id should be appended on the msg
			$requestId = $parameters['request_id'];
			$itemName = $parameters['item-name'];
			$itemQuantity = $parameters['item-quantity'];
			// search for items by the given name
			$itemsFound = $itemsService->searchByName($itemName);
			
			if (count($itemsFound) === 0) {
				$speech = "No encontré coincidencias del artículo $itemName";
				$requestService->cleanSuggestions($requestId);
			}

			if (count($itemsFound) === 1) {
				$speech = "He añadido $itemQuantity $itemName. ¿Algo mas?";
				$requestService->addItemToRequest($requestId, $itemsFound, $itemQuantity);
				$requestService->cleanSuggestions($requestId);
			}

			if (($count = count($itemsFound)) > 1) {
				$speech = "He encontrado {$count} coincidencias de $itemName. Por favor sé mas específico...";
				$requestService->addItemsSuggestionsToRequest($requestId, $itemName);
			}
			break;

		case 'action.create-request':
			$name = $parameters['request-name'];
			$newRequest = $requestService->createByName($name);
			$speech = "¿Qué artículos añado a solicitud \"$name\"?";
			$data['request_id'] = $newRequest->getKey();
			$contextOut[] = [
				'name' => $intent,
				'lifespan' => 5,
				'parameters' => [ 'request_id' => $newRequest->getKey() ]
			];
			break;

		case 'action.finish-request':
			$requestId = $parameters['request_id'];
			$requestService->finish($requestId);
			$speech = "Solicitud finalizada, ahora está pendiente de aprobación. Fue un gusto ayudarte.";
			break;

		case 'action.cancel-request':
			$requestId = $parameters['request_id'];
			$requestService->cancel($requestId);
			$speech = "Se ha cancelado tu solicitud. Fue un gusto asistirte.";
			break;

		case 'action.approve-request':
			$requestName = $parameters['request-name'];
			$requestsFound = $requestService->searchByName($requestName);
			
			switch (count($requestsFound)) {
				case 0:
					$speech = "No encontré solicitud con ese nombre, intenta otro.";
					break;

				case 1:
					$approvedRequest = $requestService->approveRequest(array_keys($requestsFound)[0]);
					$name = $requestsFound[array_keys($requestsFound)[0]]['name'];
					$speech = 'Solicitud "'. $name .'" aprovada correctamente. Ya no te ayudo mas... Ve y llena los formularios.';
					break;
				
				default:
					$speech = "Encontré ". count($requestsFound) . " coincidencias. ¿Cual deseas aprobar?";
					foreach ($requestsFound as $key => $value) {
						$data['matches'][] = ['key' => $key] + $value;
					}
					break;
			}
			break;

		case 'action.select-request-to-approve':
			$requestName = $parameters['request-name'];
			$selectedIndex = (int) $parameters['selected'] - 1;
			$requestsFound = $requestService->searchByName($requestName);
			$requestIdToApprove = array_keys($requestsFound)[$selectedIndex];
			$requestService->approveRequest($requestIdToApprove);
			$name = $requestsFound[array_keys($requestsFound)[$selectedIndex]]['name'];

			$speech = 'La solicitud "'. $name .'" fue aprovada correctamente.';
			break;

		default:
			$speech = 'Parece que ha ocurrido un error en la API';
			break;

	}

	return [
		'speech' => $speech,
		'displayText' => $speech,
		'data' => $data,
		'contextOut' => $contextOut
	];
});

// endpoint enabled for non production environments
if (env('APP_ENV') !== 'production') {

	Route::get('get_test_token', function(Request $request) {
	    $customToken = (new FirebaseService())
	    	->tokenHandler
	    	->createCustomToken($request->get('user_id', 'Pruebas_CSTN'));

	    return ['custom_token' => (string) $customToken];
	});

}

