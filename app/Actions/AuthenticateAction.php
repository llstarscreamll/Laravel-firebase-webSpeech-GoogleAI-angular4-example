<?php

namespace App\Actions;

use App\Tasks\DecryptTask;
use App\Tasks\ParseTokenTask;
use App\Tasks\CheckUserOnFirebaseTask;
use App\Services\FirebaseService;

/**
* AuthenticateAction Class.
*
* @author Johan Alvarez <llstarscreamll@hotmail.com>
*/
class AuthenticateAction
{
	/**
	 * Result message from this action.
	 * @var string
	 */
	public $msg = '';

	public function __construct()
	{
		$this->decryptTask = new DecryptTask();
		$this->parseTokenTask = new ParseTokenTask();
		$this->checkUserOnFirebaseTask = new CheckUserOnFirebaseTask();
		
		$this->tokenHandler = (new FirebaseService())->tokenHandler;
	}

	public function run($token)
	{
		// decrypt token
		$decrypted = $this->decryptTask->run($token);
		// parse the token
		$parsedToken = $this->parseTokenTask->run($decrypted);
		// check if the given user id on $parsedToken exists on Firebase DB
		$user = $this->checkUserOnFirebaseTask->run($parsedToken->userId);
		// get custom Firebase token based on the user found in Firabase
		$customToken = (string) $this->tokenHandler->createCustomToken($user->id);

		return $customToken;
	}
}