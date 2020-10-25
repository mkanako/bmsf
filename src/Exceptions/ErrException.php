<?php

namespace Cc\Bmsf\Exceptions;

use Exception;

class ErrException extends Exception
{
    public function render($request)
    {
        return err($this->getMessage());
    }
}
