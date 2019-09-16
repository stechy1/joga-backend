<?php


namespace app\model\manager\lecture_types;


use Exception;

/**
 * Class LectureTypeDataException
 * Vyjímka reprezentující chybu při zpracování dat
 * @package app\model\manager\lecture_types
 */
class LectureTypeDataException extends Exception {

    /**
     * LectureTypeDataException constructor.
     * @param string $message Zpráva popisující chybu
     */
    public function __construct(string $message) {
        parent::__construct($message);
    }
}