<?php

namespace App\Exceptions;

use Exception;

class PermissionDeniedException extends Exception
{
    public function __construct(string $message = 'Permission denied')
    {
        parent::__construct($message);
    }
}