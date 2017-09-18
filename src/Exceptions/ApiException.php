<?php

namespace Befrest\Exceptions;

class ApiException extends \Exception
{
	public function getName()
    {
        return 'ApiException';
    }
}

?>
