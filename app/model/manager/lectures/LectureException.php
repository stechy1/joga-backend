<?php


namespace app\model\manager\lectures;


use Exception;

/**
 * Class LectureException
 * Vyjímka reprezentující chybu při vyhledávání v lekcích
 * @package app\model\manager\lectures
 */
class LectureException extends Exception {

    /**
     * LectureException constructor.
     * @param string $message Zpráva popisující chybu
     */
    public function __construct(string $message) {
        parent::__construct($message);
    }
}