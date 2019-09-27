<?php


namespace app\controller;


use app\model\http\IResponse;
use Logger;

class BaseApiController extends BaseController {

    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var string
     */
    private $responseMessage;
    /**
     * @var int
     */
    private $responseMessageType = Constants::RESPONSE_MESSAGE_TYPE_SUCCESS;

    public function __construct() {
        parent::__construct();
        $this->logger = Logger::getLogger(__CLASS__);
    }

    protected function setResponseMessage(string $message, int $type = Constants::RESPONSE_MESSAGE_TYPE_SUCCESS) {
        $this->responseMessage = $message;
        $this->responseMessageType = $type;
}

    protected function sendResponse(IResponse $response): void {
        if (isset($this->responseMessage)) {
            $message = [];
            $message['message'] = $this->responseMessage;
            $message['type'] = $this->responseMessageType;
            $response->addData(Constants::RESPONSE_MESSAGE, $message);
        }

        $this->logger->trace(json_encode($response->getData()));
        $this->logger->trace("HTTP response code: " . $response->getCode());
        http_response_code($response->getCode());
        echo json_encode($response->getData());
    }
}