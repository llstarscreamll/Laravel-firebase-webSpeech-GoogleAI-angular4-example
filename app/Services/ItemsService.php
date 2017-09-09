<?php

namespace App\Services;

/**
* ItemsService Class.
*
* @author Johan Alvarez <llstarscreamll@hotmail.com>
*/
class ItemsService
{
	private $node = 'items';
	
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
		    ->startAt($name)
		    ->getValue();
	}

	public function deleteById($id)
	{
		return $this->database->getReference($this->node.'/'.$id)->remove();
	}
}