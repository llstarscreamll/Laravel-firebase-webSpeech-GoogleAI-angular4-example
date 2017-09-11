<?php

namespace App\Services;

/**
* RequestService Class.
*
* @author Johan Alvarez <llstarscreamll@hotmail.com>
*/
class RequestService
{
	private $node = 'speech_requests';
	
	public function __construct(
		FirebaseService $firebaseService,
		ItemsService $itemsService
	) {
		$this->database = $firebaseService->database;
		$this->itemsService = $itemsService;
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

	public function addItemsSuggestionsToRequest($speechRequestId, $itemName)
	{
		// search items based on the given name
		$items = $this->itemsService->searchByName($itemName);
		$this->database
			->getReference($this->node.'/'.$speechRequestId.'/suggestions')
			->set($items);

		return $items;
	}

	public function addItemToRequest(string $requestId, array $items, int $quantity)
	{
		$this->database
			->getReference($this->node.'/'.$requestId.'/items')
			->push(['item' => $items, 'quantity' => $quantity ]);
	}

	public function cleanSuggestions(string $requestId) {
		$this->database
			->getReference($this->node.'/'.$requestId.'/suggestions')
			->remove();
	}

	public function finish(string $id)
	{
		$this->database
			->getReference($this->node.'/'.$id.'/status')
			->set('finished');
	}

	public function cancel(string $id)
	{
		$this->database
			->getReference($this->node.'/'.$id.'/status')
			->set('cenceled');
	}

	public function deleteById($id)
	{
		$this->database->getReference($this->node.'/'.$id)->remove();
	}

	public function deleteAll()
	{
		$this->database->getReference($this->node)->remove();
	}
}