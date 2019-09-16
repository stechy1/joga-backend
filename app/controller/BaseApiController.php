<?php


namespace app\controller;


use app\model\util\StatusCodes;
use Logger;

class BaseApiController extends BaseController {

    private const RESPONSE_MESSAGE = "response_message";
    protected const RESPONSE_MESSAGE_TYPE_SUCCESS = 0;
    protected const RESPONSE_MESSAGE_TYPE_INFO = 1;
    protected const RESPONSE_MESSAGE_TYPE_WARNING = 2;
    protected const RESPONSE_MESSAGE_TYPE_ERROR = 3;

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
    private $responseMessageType = self::RESPONSE_MESSAGE_TYPE_SUCCESS;

    public function __construct() {
        parent::__construct();
        $this->logger = Logger::getLogger(__CLASS__);
    }

    protected function setResponseMessage(string $message, int $type = self::RESPONSE_MESSAGE_TYPE_SUCCESS) {
        $this->responseMessage = $message;
        $this->responseMessageType = $type;
}

    protected function sendResponce() {
        $this->setCode(StatusCodes::OK);
        if (isset($this->responseMessage)) {
            $message = [];
            $message['message'] = $this->responseMessage;
            $message['type'] = $this->responseMessageType;
            $this->addData(self::RESPONSE_MESSAGE, $message);
        }

        echo json_encode($this->data);
    }
}