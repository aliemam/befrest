<?php

namespace Aliemam\Befrest\Exceptions;

class ApiException extends \Exception
{
	public function getName()
    {
        return 'ApiException';
    }
}

?>
