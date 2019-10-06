<?php


namespace app\controller;


use app\model\http\IRequest;
use app\model\http\IResponse;
use app\model\service\ParsedownService;
use Logger;

/**
 * Class ApiMarkdownController
 * Markdown kontroler pro překlad markdown dokumentů na validní HTML
 * @Inject ParsedownService
 * @package app\controller
 */
class ApiMarkdownController extends BaseApiController {

    const PARAM_CONTENT = "content";
    const PARAM_HTML = "html";

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var ParsedownService
     */
    private $parsedownservice;

    public function __construct() {
        parent::__construct();
        $this->logger = Logger::getLogger(__CLASS__);
    }

    public function defaultPOSTAction(IRequest $request, IResponse $response) {
        $content = $request->get(self::PARAM_CONTENT);

        $html = $this->parsedownservice->encode($content);
        $response->addData(self::PARAM_HTML, $html);
    }


}