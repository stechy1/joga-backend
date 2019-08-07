<?php

namespace app\model\service\request;


use Logger;

class Request implements IRequest {

    private $logger;

    private $controller;
    private $action;
    private $requestMethod;
    private $params;
    private $data;
    private $files;

    /**
     * Request constructor
     *
     * @param $controller string Název obslužného kontroleru
     * @param $action string Akce, která se má vykonat
     * @param string $requestMethod Typ requestu
     * @param $params array Pole parametrů
     * @param $data array Pole parametrů v postu
     * @param $files array Pole nahraných souborů
     */
    public function __construct(string $controller, string $action, string $requestMethod, array $params, array $data, array $files) {
        $this->logger = Logger::getLogger(__CLASS__);
        $this->controller = $controller;
        $this->action = $action;
        $this->requestMethod = $requestMethod;
        $this->params = $params;
        $this->data = $data;
        $this->files = $files;

        $this->logger->trace("Byl vytvořen nový request: " . $this->__toString());
    }

    function getController(): string {
        return $this->controller;
    }

    function getAction(): string {
        return $this->action;
    }

    function getDefaultAction(): string {
        $this->action = 'default' . $this->requestMethod . 'Action';
        return $this->action;
    }

    function get($key = null, $default = null) {
        if (func_num_args() === 0) {
            return $this->data;

        } elseif (isset($this->data[$key])) {
            return $this->data[$key];

        } else {
            return $default;
        }
    }

    function getFile($key): FileEntry {
        return isset($this->files[$key]) ? new FileEntry($this->files[$key]) : null;
    }

    function getFiles() {
        return $this->files;
    }

    function getParams() {
        return $this->params;
    }

    function hasParams($minCount = 0) {
        return sizeof($this->params) - 1 >= $minCount;
    }

    function hasFiles() {
        return !empty($this->files);
    }

    function getHeaders() {
        return getallheaders();
    }

    public function __toString() {
        return sprintf("Controller: %s --> %s; Params: %s; Data: %s Files: %s.", $this->controller, $this->action, json_encode($this->params), json_encode($this->data), json_encode($this->files));
    }

}