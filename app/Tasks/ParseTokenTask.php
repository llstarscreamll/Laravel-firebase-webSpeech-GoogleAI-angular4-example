<?php

// use stdClass;

namespace App\Tasks;

/**
* ParseTokenTask Class.
*
* @author Johan Alvarez <llstarscreamll@hotmail.com>
*/
class ParseTokenTask
{
	public function run(string $value)
	{
		$parsedToken = new \stdClass();
		$data = [];

		if ($value) {
			$data = explode(';', $value);
		}

		$parsedToken->userId = str_replace('empid=', '', $data[0]);
		$parsedToken->timestamp = str_replace('timestamp=', '', $data[1]);
		$parsedToken->error = str_replace('ERROR=', '', $data[2]);
		$parsedToken->logout = str_replace('LOGOUT=', '', $data[3]);
		$parsedToken->timeout = str_replace('TIMEOUT=', '', $data[4]);

		return $parsedToken;
	}
}
