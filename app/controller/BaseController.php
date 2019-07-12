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
    private $data = [];

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

    /**
     * Provede se před hlavním zpracováním požadavku v kontroleru
     */
    public function onStartup() {}

    /**
     * Provede se po zpracování hlavního požadavku v kontroleru
     */
    public function onExit() {}

    /**
     * Výchozí akce kontroleru
     *
     * @param IRequest $request
     */
//    public function defaultAction(IRequest $request) {
//        $this->logger->info("Výchozí akce kontroleru.");
//        $this->addData("controller", $request->getController());
//        $this->addData("action", $request->getAction());
//        $this->addData("data", $request->getParams());
//    }

    public function defaultGETAction(IRequest $request) {
        $this->logger->info("defaultGETAction");
    }

    public function defaultPOSTAction(IRequest $request) {
        $this->logger->info("defaultPOSTAction");
    }

    public function defaultPUTAction(IRequest $request) {
        $this->logger->info("defaultPUTAction");
    }

    public function defaultDELETEAction(IRequest $request) {
        $this->logger->info("defaultDELETEAction");
    }

    protected function sendResponce() {
        echo json_encode($this->data);
    }
}