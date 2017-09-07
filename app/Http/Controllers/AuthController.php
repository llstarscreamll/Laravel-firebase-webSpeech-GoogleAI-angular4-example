<?php

namespace App\Http\Controllers;

use App\Tasks\DecryptTask;
use Illuminate\Http\Request;
use App\Tasks\ParseUserDataTask;
use App\Actions\AuthenticateAction;

/**
* AuthController Class.
*
* @author Johan Alvarez <llstarscreamll@hotmail.com>
*/
class AuthController extends Controller
{
	/**
	 * @var App\Actions\AuthenticateAction.
	 */
	private $authAction;
	/**
	 * Create a new AuthController instance.
	 */
	public function __construct(AuthenticateAction $authAction)
	{
		$this->authAction = $authAction;
	}

	/**
	 * Login the user with the given token on request.
	 *
	 * @param  Request $request
	 * @return Response
	 */
	public function login(Request $request)
	{
		// if error is present on request, show error on page
		if ($request->has('error')) {
			$error = bin2Hex($request->get('error'));

			return redirect('/error?'.http_build_query(['error' => $error]));
		}

		// token must be present in request
		if (! $request->has('key')) {
			$error = 'No se han recivido datos de token.';

			return redirect('/error?error='.bin2Hex($error));
		}

		// run authenticate action
		$customToken = $this->authAction->run($request->get('key'));

		// if no token provided, go to error page
		if (! $customToken) {
			return redirect('/error');
		}

		// all ok, go to home and render the Angular app
		return redirect('/?token='.$customToken);
	}
}
