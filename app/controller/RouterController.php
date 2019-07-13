<?php

namespace app\controller;


use app\model\service\Container;
use app\model\service\request\IRequest;
use Logger;

/**
 * Class RouterController
 * @Inject Container
 * @package app\controller
 */
class RouterController extends BaseController {

    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var Container
     */
    private $container;
    /**
     * @var BaseController
     */
    protected $controller;

    /**
     * RouterController constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->logger = Logger::getLogger(__CLASS__);
    }

    private function extractJWT() {

    }



    /**
     * Výchozí akce kontroleru
     * @param IRequest $request
     */
    public function defaultAction(IRequest $request) {
        $controller = $request->getController() . 'controller';
        $this->logger->trace("Budu zpracovávat kontroller: " . $controller);

        $this->controller = $this->container->getInstanceOf($controller);
        if ($this->controller == null) {
            $this->logger->error("Kontroller nebyl nalezen.");
            http_response_code(404);
            echo $controller;
            exit(-1);
        }

        $this->logger->trace("Controller -> onStartup().");
        $this->controller->onStartup();

        $action = $request->getAction();
        $this->logger->trace("Rozpoznal jsem akci kontroleru na: " . $action);

        if (!method_exists($this->controller, $action)) {
            $this->logger->trace("Akce nebyla nalezena, používám výchozí.");
            $action = $request->getDefaultAction();
        }

        try {
            call_user_func_array(array($this->controller, $action), array($request));
        } catch (\Exception $ex) {
            $this->logger->fatal($ex->getMessage());
            $this->logger->debug($ex->getTraceAsString());
        }

        $this->logger->trace("Controller -> onExit().");
        $this->controller->onExit();

        $this->logger->trace("Odesílám odpověď.");
        $this->controller->sendResponce();
    }
}