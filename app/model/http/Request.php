<?php

namespace app\model\http;


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

    function get(string $key = null, bool $mandatory = true, $default = null) {
        if (func_num_args() === 0) {
            return $this->data;

        }

        if (!isset($this->data[$key])) {
            if ($mandatory) {
                throw new BadQueryStringException("Parametr '" . $key . "' nebyl nalezen!");
            }

            return $default;
        }

        return $this->data[$key];
    }

    function getFile(string $key, bool $mandatory = true): FileEntry {
        if (!isset($this->files[$key])) {
            if ($mandatory) {
                throw new BadQueryStringException("Soubor nebyl přiložen!");
            }

            return null;
        }

        return new FileEntry($this->files[$key]);
    }

    function getFiles(): iterable {
        $entries = [];
        foreach ($this->files as $file) {
            $entries[] = new FileEntry($file);
        }

        return $entries;
    }

    function getParam(int $index = -1, bool $mandatory = true) {
        if ($index == -1) {
            return $this->params;
        }

        if (!isset($this->params[$index])) {
            if ($mandatory) {
                throw new BadQueryStringException("Parametr " . $index . " neexistuje!");
            }

            return null;
        }

        return $this->params[$index];
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

    function spliceParams() {
        $this->params = array_slice($this->params, 1);
        $this->logger->trace("Param array was spliced: " . $this->__toString());
    }

    public function __toString() {
        return sprintf("Controller: %s --> %s; Params: %s; Data: %s Files: %s.", $this->controller, $this->action, json_encode($this->params), json_encode($this->data), json_encode($this->files));
    }

}