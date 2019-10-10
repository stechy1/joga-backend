<?php


use app\App;
use app\model\service\Container;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define("__HOME__", $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST']);
define("__IS_WINDOWS__", strpos($_SERVER['DOCUMENT_ROOT'], "\\") !== false);
define("__PUBLIC_ROOT__", __DIR__);
date_default_timezone_set('Europe/Prague');
$loader = require "../vendor/autoload.php";

Logger::configure("../app/config/log4php.xml");
$logger = Logger::getLogger("index");

/** @var Container $container */
$container = require("../app/bootstrap.php");

try {
    /** @var App $app */
    $app = $container->getInstanceOf('app');
    $app->run();
} catch (Exception $ex) {
    $logger->fatal($ex->getMessage());
    $logger->fatal($ex->getTraceAsString());
}