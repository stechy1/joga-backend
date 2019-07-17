<?php


use app\App;
use app\model\service\Container;

//session_start();

define("__HOME__", $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST']);
define("__IS_WINDOWS__", strpos($_SERVER['DOCUMENT_ROOT'], "\\") !== false);
date_default_timezone_set('Europe/Prague');
$loader = require "vendor/autoload.php";

Logger::configure("app/config/log4php.xml");

/** @var Container $container */
$container = require("app/bootstrap.php");
/** @var App $app */
$app = $container->getInstanceOf('app');
$app->run();