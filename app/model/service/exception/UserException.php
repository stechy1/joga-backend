<?php

namespace app\model\service\exception;

use Exception;

/**
 * Class UserException
 * Vyjímka používaná správcem uživatelů
 * @package app\model\service\exception
 */
class UserException extends Exception {


    /**
     * UserException constructor.
     * @param string $message Důvod vyjímky
     */
    public function __construct(string $message) {
        parent::__construct($message);
    }
}