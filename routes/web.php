<?php

use Illuminate\Http\Request;
use App\Services\FirebaseService;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function (Request $request) {
	// check if there are not token data on session or
	// request, redirect to error page
	if (! $request->has('token') && ! session()->has('token')) {
		$error = 'Tienes que iniciar sesión.';
		return redirect('/error?error='.bin2hex($error));
	}

	// if request has token data, put it on session and clean
	// the token from the url
	if ($request->has('token')) {
		$token = $request->get('token');
		session()->put('token', $token);
		return redirect('/');
	}

	// if session hasn't token, then go to error page, session expired
	if (! session()->has('token')) {
		$error = 'Sesión expirada.';
		return redirect('/error?error='.bin2hex($error));
	}

	// all ok here, render de Angular app
	return view('angular-app');
});

Route::get('/error', function (Request $request) {
	$error = $request->has('error')
		? hex2Bin($request->get('error'))
		: 'Error desconocido';

    return view('welcome', ['error' => $error]);
});
