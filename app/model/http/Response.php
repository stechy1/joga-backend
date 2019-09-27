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
     * @var array $flowData Data used in the controlers live cycle
     */
    private $flowData = [];

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

    public function addFlowData(string $key, $value): void {
        $this->flowData[$key] = $value;
    }

    public function getFlowData(string $key, string $defaultValue = null) {
        if (!isset($this->flowData[$key])) {
            return $defaultValue;
        }

        return $this->flowData[$key];
    }

    public function setCode(int $code): void {
        $this->logger->trace("Nastavuji status code na: " . $code);
        $this->httpCode = $code;
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