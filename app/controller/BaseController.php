<?php

namespace app\controller;


use app\middleware\IMiddleware;
use app\model\http\IRequest;
use app\model\http\IResponse;
use Logger;

/**
 * Class BaseController
 * Třída poskytující základní funkce pro všechny kontrolery
 * @package app\controller
 */
abstract class BaseController {

    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var IMiddleware[]
     */
    protected $middlewares;

    public function __construct() {
        $this->logger = Logger::getLogger(__CLASS__);
    }

    public function installMiddleware(string $middleware) {
        $this->middlewares[] = $middleware;
    }

    /**
     * Provede se před hlavním zpracováním požadavku v kontroleru
     *
     * @param IRequest $request Požadavek, který přišel
     * @param IResponse $response Odpověď ze serveru
     */
    public function onStartup(IRequest $request, IResponse $response) {
    }

    /**
     * Provede se po zpracování hlavního požadavku v kontroleru
     */
    public function onExit() {
    }

    public function defaultGETAction(IRequest $request, IResponse $response) {
        $this->logger->info("defaultGETAction");
    }

    public function defaultPOSTAction(IRequest $request, IResponse $response) {
        $this->logger->info("defaultPOSTAction");
    }

    public function defaultPUTAction(IRequest $request, IResponse $response) {
        $this->logger->info("defaultPUTAction");
    }

    public function defaultPATCHAction(IRequest $request, IResponse $response) {
        $this->logger->info("defaultPATCHAction");
    }

    public function defaultDELETEAction(IRequest $request, IResponse $response) {
        $this->logger->info("defaultDELETEAction");
    }

    /**
     * Postará se o odeslání odpovědi
     *
     * @param IResponse $response Rozhraní obsahující data pro odpověď ze serveru
     */
    protected abstract function sendResponse(IResponse $response): void;
}