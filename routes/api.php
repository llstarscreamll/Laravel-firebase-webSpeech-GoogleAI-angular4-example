<?php

use Illuminate\Http\Request;
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
	$intent = $data['result']['metadata']['intentName'];
	$msg = $data['result']['resolvedQuery'];
	$speech = "Opps!! Algo ha salido mal..";

	switch ($intent) {

		case 'buscar-articulo':
			// the speech request id should be appended on the msg
			$speechRequestId = explode(':', $msg)[1];
			$itemName = explode(':', $msg)[0];
			// add items sugestions based on the given $msg
			$itemsSuggested = $requestService->addItemsSuggestionsToRequest($speechRequestId, $itemName);
			// set response msg
			$speech = ($itemsCount = count($itemsSuggested)) > 0
				? "Se encontraron $itemsCount artículos, cual eliges?"
				: "No se encontraron sugerencias...";
			break;

		case 'nombre-solicitud':
			$requestedName = $msg;
			$speechRequests = $requestService->createByName($requestedName);
			$speech = 'Tu solicitud ha sido creada, dime qué artículo busco para añadir:'.$speechRequests->getKey();
			break;
		
		default:
			$speech = 'Parece que ha ocurrido un error en la API';
			break;

	}

	return [
		'speech' => $speech,
		'displayText' => $speech,
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

