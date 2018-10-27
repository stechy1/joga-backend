<?php

namespace app\controller;


use app\model\service\Container;
use app\model\service\request\IRequest;

/**
 * Class RouterController
 * @Inject Container
 * @package app\controller
 */
class RouterController extends BaseController {

    /**
     * @var Container
     */
    private $container;
    /**
     * @var BaseController
     */
    protected $controller;

    /**
     * Výchozí akce kontroleru
     * @param IRequest $request
     */
    public function defaultAction(IRequest $request) {
        $controller = $request->getController() . 'controller';

        $this->controller = $this->container->getInstanceOf($controller);
        if ($this->controller == null) {
            http_response_code(404);
            exit(-1);
        }

        $this->controller->onStartup();

        $action = $request->getAction();

        if (!method_exists($this->controller, $action)) {
            $action = 'defaultAction';
        }

        try {
            call_user_func_array(array($this->controller, $action), array($request));
        } catch (\Exception $ex) {
            $l = \Logger::getLogger("RouterController");
            $l->fatal($ex->getMessage());
            $l->debug($ex->getTraceAsString());
        }

        $this->controller->onExit();

        $this->controller->sendResponce();
    }
}