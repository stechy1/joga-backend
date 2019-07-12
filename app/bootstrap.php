<?php


use app\model\service\Container;

require "config/config.php";

define("__APP_ROOT__", __DIR__);

/**
 * $container Container
 */
$container = Container::getContainer();
$container->registerFolder(__DIR__);

return $container;