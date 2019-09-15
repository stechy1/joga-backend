<?php

namespace app\controller;


use app\model\service\request\IRequest;
use Logger;

/**
 * Class BaseController
 * Třída poskytující základní funkce pro všechny kontrolery
 * @package app\controller
 */
abstract class BaseController {

    private $logger;
    /**
     * @var array $data
     */
    protected $data = [];
    /**
     * @var array $flowData Data used in the controlers live cycle
     */
    protected $flowData = [];
    /**
     * @var int
     */
    private $code = -1;

    public function __construct() {
        $this->logger = Logger::getLogger(__CLASS__);
    }

    /**
     * Přidá data, která putujou ke klientovi
     *
     * @param string $key
     * @param mixed $value
     * @param bool $jsonEncode False, pokud se nemají data enkodovat do jsonu, výchozi je true
     */
    protected function addData(string $key, $value, $jsonEncode = true) {
        if ($jsonEncode || is_object($value)) {
            $this->data[$key] = json_encode($value);
        }

        $this->data[$key] = $value;
    }

    protected function checkUser() {

    }

    protected function setCode(int $code) {
        if (!$this->code == -1) {
            http_response_code($code);
            $this->code = $code;
        }
    }

    protected function setHeader(string $name, string $value) {
        if ($value === null) {
            header_remove($name);
        } else {
            header($name . ': ' . $value, true, $this->code);
        }
    }

    /**
     * Provede se před hlavním zpracováním požadavku v kontroleru
     *
     * @param IRequest $request Požadavek, který přišel
     */
    public function onStartup(IRequest $request) {
    }

    /**
     * Provede se po zpracování hlavního požadavku v kontroleru
     */
    public function onExit() {
    }

    public function defaultGETAction(IRequest $request) {
        $this->logger->info("defaultGETAction");
    }

    public function defaultPOSTAction(IRequest $request) {
        $this->logger->info("defaultPOSTAction");
    }

    public function defaultPUTAction(IRequest $request) {
        $this->logger->info("defaultPUTAction");
    }

    public function defaultPATCHAction(IRequest $request) {
        $this->logger->info("defaultPATCHAction");
    }

    public function defaultDELETEAction(IRequest $request) {
        $this->logger->info("defaultDELETEAction");
    }

    protected abstract function sendResponce();
}