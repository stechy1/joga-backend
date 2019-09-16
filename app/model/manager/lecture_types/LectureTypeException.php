<?php


namespace app\model\manager\lecture_types;


use Exception;

/**
 * Class LectureTypeException
 * Vyjímka reprezentující chybu při vyhledávání v typech lekcí
 * @package app\model\manager\lecture_types
 */
class LectureTypeException extends Exception {

    /**
     * LectureTypeException constructor.
     * @param string $message Zpráva popisující chybu
     */
    public function __construct(string $message) {
        parent::__construct($message);
    }
}