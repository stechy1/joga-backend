<?php

namespace app\model\factory;


use app\model\service\request\Request;
use app\model\util\StringUtils;

class RequestFactory {

    /**
     * Naparsuje URL adresu podle lomítek a vrátí pole parametrů
     *
     * @param $url string URL pro naparsování
     * @return array Pole(1. proměnná je vždy kontroler, zbytek jsou proměnný)
     */
    private function parseURL(string $url) {
        // Naparsuje jednotlivé části URL adresy do asociativního pole
        $parsedURL = parse_url($url);
        // Odstranění počátečního lomítka
        $parsedURL["path"] = ltrim($parsedURL["path"], "/");
        // Odstranění bílých znaků kolem adresy
        $parsedURL["path"] = trim($parsedURL["path"]);
        // Rozbití řetězce podle lomítek
        $partedWay = explode("/", $parsedURL["path"]);
        return $partedWay;
    }

    /**
     * Vytvoří nový request
     *
     * @return Request
     */
    public function createHttpRequest() {
        $requestUrl = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
        $parsedURL = $this->parseURL($requestUrl);

        $controller = (!empty($parsedURL[0]) ? StringUtils::hyphensToCamel(array_shift($parsedURL)) : 'default');
        if ($controller === 'api') {
            $controller .= !empty($parsedURL[0]) ? StringUtils::hyphensToCamel(array_shift($parsedURL)) : 'default';
            if ($controller === 'apiadmin') {
                $controller .= !empty($parsedURL[0]) ? StringUtils::hyphensToCamel(array_shift($parsedURL)) : 'default';
            }
        }
        $requestMethod = $_SERVER['REQUEST_METHOD'] | "";
        $action = (!empty($parsedURL[0]) ? StringUtils::hyphensToCamel($parsedURL[0]) : 'default');
        $action = $action . $requestMethod;
        $params = $parsedURL;

        $action .= 'Action';

        return new Request($controller, $action, $requestMethod, $params, $_POST, $_FILES);
    }
}