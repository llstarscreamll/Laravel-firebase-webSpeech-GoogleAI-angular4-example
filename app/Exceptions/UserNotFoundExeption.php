<?php

namespace App\Exceptions;

use Exception;

/**
* UserNotFoundExeption Class.
*
* @author Johan Alvarez <llstarscreamll@hotmail.com>
*/
class UserNotFoundExeption extends Exception
{
	function __construct($message = "User not found")
	{
		parent::__construct($message);
	}
}