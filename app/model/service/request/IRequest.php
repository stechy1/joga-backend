<?php

namespace app\model\service\request;


interface IRequest {

    const
        HEAD = 'HEAD', GET = 'GET', POST = 'POST', PUT = 'PUT', DELETE = 'DELETE';

    /**
     * Získá název kontroleru
     *
     * @return string Vrátí název kontrolleru
     */
    function getController();

    /**
     * Získá název akce, která se má provést v kontroleru
     *
     * @return string Vrátí název akce, která se má provést
     * Struktura názvu:
     *   název metoda
     *         metoda = [HEAD, GET, POST, PUT, DELETE]
     *   UserGET, UserPOST, UserPUT, UserDELETE
     */
    function getAction();

    function getDefaultAction();

    /**
     * Vrátí hodnotu uloženou v postu na daném klíči. Pokud hodnota neexistuje, vrátí výchozí hodnotu
     *
     * @param null $key Klíč hledané hodnoty
     * @param null $default Výchozí hodnota, pokud není v postu
     * @return mixed Hodnotu z postu nebo výchozí hodnotu
     */
    function get($key = null, $default = null);

    /**
     * Vrátí nahraný soubor
     *
     * @param $key string Klíč, pod kterým se má soubor nacházet
     *
     * @return array|null
     */
    function getFile($key);

    /**
     * Vrátí pole nahraných souborů
     *
     * @return array
     */
    function getFiles();

    /**
     * Vrátí pole parametrů
     *
     * @return array Pole naparsovaných parametrů z adresy
     */
    function getParams();

    /**
     * Zkontroluje, zda-li požadavek obsahuje nějaké parametry
     *
     * @param int $minCount Minimální počet požadavků
     * @return bool True, pokud request obsahuje parametry, jinak false
     */
    function hasParams($minCount = 0);

    /**
     * Zkontroluje, zda-li požadavek obsahuje nahrané soubory
     *
     * @return boolean True, pokud požadavek obsahuje nějaké uživatelem nahrané soubory, jinak false
     */
    function hasFiles();
}