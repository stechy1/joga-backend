<?php

namespace app\controller;


use app\model\http\IResponse;
use app\model\service\Container;
use app\model\http\IRequest;
use Exception;
use Logger;
use ReflectionException;

/**
 * Class RouterController
 * Speciální typ kontroleru, který se stará o směrování požadavků
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

    /**
     * Výchozí akce kontroleru
     * @param IRequest $request Rozhraní reprezentuící požadavek od klienta
     * @param IResponse $response Rozhraní reprezentující odpověď ze serveru
     * @throws ReflectionException Pokud se nepodaří najít odpovídající třídu
     */
    public function defaultAction(IRequest $request, IResponse $response) {
        $controller = $request->getController() . 'controller';
        $this->logger->trace("Budu zpracovávat kontroller: " . $controller);

        $this->controller = $this->container->getInstanceOf($controller);
        if ($this->controller == null) {
            $this->logger->error("Kontroller " . $controller . " nebyl nalezen!");
            http_response_code(404);
            echo $controller;
            exit(-1);
        }

        $this->logger->trace("Controller -> onStartup().");
        $this->controller->onStartup($request, $response);

        foreach ($this->middlewares as $middleware) {
            $this->logger->trace($middleware);
            try {
                $this->container->getInstanceOf($middleware);
            } catch (Exception $ex) {
                $this->logger->error($ex->getMessage());
                $this->logger->error($ex);

            }
            //$middleware->apply($request, $response);
        }

        $action = $request->getAction();
        $this->logger->trace("Rozpoznal jsem akci kontroleru na: " . $action);

        if (!method_exists($this->controller, $action)) {
            $this->logger->trace("Akce nebyla nalezena, používám výchozí.");
            $action = $request->getDefaultAction();
        } else {
            if (count($request->getParams()) > 1) {
                $request->spliceParams();
            }
        }

        try {
            call_user_func_array(array($this->controller, $action), array($request, $response));
        } catch (Exception $ex) {
            $this->logger->fatal($ex->getMessage());
            $this->logger->debug($ex->getTraceAsString());
        }

        $this->logger->trace("Controller -> onExit().");
        $this->controller->onExit();

        $this->sendResponse($response);
    }

    protected function sendResponse(IResponse $response): void {
        $this->logger->trace("Odesílám odpověď.");
//        $this->logger->trace(json_encode($response));
        $this->controller->sendResponse($response);
    }
}