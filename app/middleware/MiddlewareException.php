<?php


namespace app\middleware;


use Exception;

class MiddlewareException extends Exception {

    /**
     * MiddlewareException constructor.
     * @param string $message
     */
    public function __construct(string $message) {
        parent::__construct($message);
    }
}