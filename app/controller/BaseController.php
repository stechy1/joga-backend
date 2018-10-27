<?php

namespace app\controller;


use app\model\callback\AjaxCallBack;
use app\model\callback\CallBackData;
use app\model\service\request\IRequest;


/**
 * Class BaseController
 * @package app\controller
 */
abstract class BaseController {

    /**
     * @var array $data
     */
    private $data = [];

    /**
     * Přidá data, která putujou ke klientovi
     *
     * @param string $value
     * @param mixed $value
     * @param bool $jsonEncode False, pokud se nemají data enkodovat do jsonu, výchozi je true
     */
    protected function addData(string $key, $value, $jsonEncode = true) {
        $this->data[$key] = ($jsonEncode) ? json_encode($value) : $value;
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
    public function defaultAction(IRequest $request) {
        $this->addData("controller", $request->getController());
        $this->addData("action", $request->getAction());
    }

    protected function sendResponce() {
        echo json_encode($this->data);
    }
}