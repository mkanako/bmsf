<?php

namespace Cc\Labems\Exceptions;

use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler
{
    public static function render($request, \Exception $exception)
    {
        if (in_array(current(explode('/', trim($request->getPathInfo(), '/'))), array_keys(config('labems')))) {
            if ($exception instanceof MethodNotAllowedHttpException || $exception instanceof NotFoundHttpException) {
                return err('Not Found');
            }
            return err($exception->getMessage());
        }
        return false;
    }
}
