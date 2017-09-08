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
	$intent = $data['result']['metadata']['intentName'];
	$speech = "Opps!! Algo ha salido mal..";

	if ($intent === "nombre-solicitud") {
		$requestedName = $data['result']['resolvedQuery'];

		$requestService = app(RequestService::class);
		$speechRequests = $requestService->createByName($requestedName);
		
		$speech = 'Tu solicitud creada, dime qué artículo añadir:'.$speechRequests->getKey();

		return [
		    'speech' => $speech,
		    'displayText' => $speech,
		];
	}

	return [
		'speech' => "Parece que ha ocurrido un error",
		'displayText' => "Parece que ha ocurrido un error",
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

