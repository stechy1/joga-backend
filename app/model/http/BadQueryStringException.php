<?php


namespace app\model\http;



use Exception;

/**
 * Class BadQueryStringException
 * Vyjímka reprezentující chybu při zpracování URL parametrů.
 * @package app\model\http
 */
class BadQueryStringException extends Exception {

    public function __construct(string $message) {
        parent::__construct($message);
    }
}