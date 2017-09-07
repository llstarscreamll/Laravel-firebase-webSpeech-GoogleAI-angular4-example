<?php

namespace App\Services;

use Kreait\Firebase\Factory as FirebaseFactory;
use Kreait\Firebase\ServiceAccount;

/**
* FirebaseService Class.
*
* @author Johan Alvarez <llstarscreamll@hotmail.com>
*/
class FirebaseService
{
	public $firebase;
	public $database;
	public $tokenHandler;

	/**
	 * Create new FirebaseService.
	 */
	public function __construct()
	{
		$jsonKeyPath = base_path(env('FIREBASE_JSON_KEY_PATH'));
		
		$serviceAccount = ServiceAccount::fromJsonFile($jsonKeyPath);
		$this->firebase = (new FirebaseFactory)
		    ->withServiceAccount($serviceAccount)
		    ->create();

		$this->database = $this->firebase->getDatabase();
		$this->tokenHandler = $this->firebase->getTokenHandler();
	}
}