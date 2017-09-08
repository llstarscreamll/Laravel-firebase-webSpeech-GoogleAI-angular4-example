<?php

namespace App\Services;

/**
* RequestService
*/
class RequestService
{
	
	public function __construct(FirebaseService $firebaseService)
	{
		$this->database = $firebaseService->database;
	}

	public function searchRequestByName(string $name)
	{
		return $this->database->getReference('people')
		    // order the reference's children by the values in the field 'height'
		    ->orderByChild('name')
		    // returns all persons taller than or exactly 1.68 (meters)
		    ->startAt($name)
		    ->getSnapshot();
	}
}