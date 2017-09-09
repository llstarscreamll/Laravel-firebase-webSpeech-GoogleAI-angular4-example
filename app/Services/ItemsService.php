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
		$data = $this->database->getReference($this->node)
			->orderByChild('name')
		    ->startAt($name)
		    ->limitToFirst(5)
		    ->getValue();

		// that stupid fucking Firebase database returns unuseful
		// data with the desired results... let's clean those
		// poorly results from that Firebase shit 
		return $data
			? array_where($data, function ($value, $key) use ($name) {
				return str_contains($value['name'], $name);
			})
			: [];
	}

	public function deleteById($id)
	{
		return $this->database->getReference($this->node.'/'.$id)->remove();
	}

	public function deleteAll()
	{
		$this->database->getReference($this->node)->remove();
	}
}