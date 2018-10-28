<?php

namespace app\controller\admin;


use app\controller\BaseController;

/**
 * Class AdminBaseController
 * Třída poskytující základni metody pro kontrolery v administraci
 * @package app\controller\admin
 */
abstract class AdminBaseController extends BaseController {

    public function onStartup() {
        // TODO ověřit, zda-li je uživatel přihlášenej
    }



}