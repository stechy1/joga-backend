<?php


namespace app\model\manager\user;


use Exception;

/**
 * Class UserException
 * Vyjímka používaná správcem uživatelů,
 * reprezentující chybu při manipulaci s daty uživatele
 * @package app\model\service\exception
 */
class UserDataException extends Exception {

    /**
     * UserDataException constructor
     * @param string $message Důvod vyjímky
     */
    public function __construct(string $message) {
        parent::__construct($message);
    }

}