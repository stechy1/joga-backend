<?php


namespace app\model\manager\lectures;


use Exception;

/**
 * Class LectureDataException
 * Vyjímka reprezentující chybu při zpracování dat
 * @package app\model\manager\lectures
 */
class LectureDataException extends Exception {

    /**
     * LectureDataException constructor.
     * @param string $message Zpráva popisující chybu
     */
    public function __construct(string $message) {
        parent::__construct($message);
    }

}