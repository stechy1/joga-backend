<?php

namespace app;


use app\controller\RouterController;
use app\model\database\IDatabase;
use app\model\factory\RequestFactory;
use app\model\service\Container;
use app\model\service\request\IRequest;
use Logger;


/**
 * Třída představující vstupní bod aplikace
 * @Inject Container
 * @package app
 */
class App {

    private $logger;

    /**
     * @var Container
     */
    private $container;

    public function __construct() {
        $this->logger = Logger::getLogger(__CLASS__);
    }

    public function run() {
        /**
         * @var IDatabase $database
         */
        $database = $this->container->getInstanceOf('database');
        try {
            $database->connect(DATABASE_HOST, DATABASE_LOGIN, DATABASE_PASS, DATABASE_SCHEME);
        } catch (\PDOException $ex) {
            $this->logger->fatal("Nepodarilo se pripojit k databazi. Ukoncuji relaci.");
            exit(1);
        }

        /**
         * @var $router RouterController
         */
        $router = $this->container->getInstanceOf("RouterController");
        /**
         * @var $reqFactory RequestFactory
         */
        $reqFactory = $this->container->getInstanceOf("RequestFactory");

        /**
         * @var $request IRequest
         */
        $request = $reqFactory->createHttpRequest();


        if (substr_count($request->getController(), "api") === 0) {
            include __DIR__ . '/../public/index.html';
        } else {
            $router->defaultAction($request);
        }
    }
}