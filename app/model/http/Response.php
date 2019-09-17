<?php


namespace app\model\http;


use app\model\util\StatusCodes;
use Logger;

class Response implements IResponse {

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var mixed[]
     */
    private $data;

    /**
     * @var int
     */
    private $httpCode = StatusCodes::OK;

    public function __construct() {
        $this->logger = Logger::getLogger(__CLASS__);
    }

    public function addData(string $key, $value, $jsonEncode = true): void {
        if ($jsonEncode || is_object($value)) {
            $this->data[$key] = json_encode($value);
        }

        $this->data[$key] = $value;
    }

    public function setCode(int $code): void {
        if ($this->httpCode == -1) {
            $this->logger->trace("Nastavuji status code na: " . $code);
//            http_response_code($code);
            $this->httpCode = $code;
        }
    }

    public function setHeader(string $name, string $value): void {
        if ($value === null) {
            header_remove($name);
        } else {
            header($name . ': ' . $value, true, $this->httpCode);
        }
    }

    public function getCode(): int {
        return $this->httpCode;
    }

    public function getData() {
        return $this->data;
    }


}