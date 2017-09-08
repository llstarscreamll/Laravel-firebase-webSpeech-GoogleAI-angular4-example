<?php

namespace App\Services;

/**
* RequestService
*/
class RequestService
{
	private $node = 'speech_requests';
	
	public function __construct(FirebaseService $firebaseService)
	{
		$this->database = $firebaseService->database;
	}

	public function createByName(string $name) {
		return $this->database
			->getReference($this->node)
			->push([ 'name' => $name ]);
	}

	public function searchByName(string $name)
	{
		return $this->database->getReference($this->node)
			->orderByChild('name')
		    // returns all persons taller than or exactly 1.68 (meters)
		    ->startAt($name)
		    ->getValue();
	}
}