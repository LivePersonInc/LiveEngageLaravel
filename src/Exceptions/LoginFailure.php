<?php

namespace LivePersonInc\LiveEngageLaravel\Exceptions;

use Exception;

/**
 * @codeCoverageIgnore
 */
class LoginFailure extends LiveEngageException
{
	public function __construct()
	{
		parent::__construct('Login failed. Do you have the `User Login` API enabled for this keyset?');
	}
}
