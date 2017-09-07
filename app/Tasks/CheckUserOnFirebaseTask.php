<?php

namespace App\Tasks;

use Kreait\Firebase;
use App\Services\FirebaseService;
use Firebase\Factory as FirebaseFactory;
use App\Exceptions\UserNotFoundExeption;

/**
* CheckUserOnFirebaseTask Class.
*
* @author Johan Alvarez <llstarscreamll@hotmail.com>
*/
class CheckUserOnFirebaseTask
{
	/**
	 * Firebase database instance.
	 */
	private $fbd;

	public function __construct()
	{
		$firebaseService = new FirebaseService();
		$this->fbd = $firebaseService->database;
	}

	public function run($userId)
	{
		$user = $this->fbd->getReference('users/' . $userId)->getValue();

		if (! $user) {
			throw new UserNotFoundExeption("User not found in app Database");
		}

		return (object) $user;
	}
}
