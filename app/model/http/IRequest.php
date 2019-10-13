<?php

namespace app\model\http;


use Exception;

interface IRequest {

    const
        HEAD = 'HEAD', GET = 'GET', POST = 'POST', PUT = 'PUT', PATCH = 'PATCH', DELETE = 'DELETE';

    /**
     * Získá název kontroleru
     *
     * @return string Vrátí název kontrolleru
     */
    function getController(): string;

    /**
     * Získá název akce, která se má provést v kontroleru
     *
     * @return string Vrátí název akce, která se má provést
     * Struktura názvu:
     *   název metoda
     *         metoda = [HEAD, GET, POST, PUT, DELETE]
     *   UserGET, UserPOST, UserPUT, UserDELETE
     */
    function getAction(): string;

    function getDefaultAction(): string;

    /**
     * Vrátí hodnotu uloženou v postu na daném klíči. Pokud hodnota neexistuje, vrátí výchozí hodnotu
     *
     * @param string $key Klíč hledané hodnoty
     * @param bool $mandatory True, pokud je parametr povinný, jinak false
     * @param null $default Výchozí hodnota, pokud není v postu
     * @return mixed Hodnotu z postu nebo výchozí hodnotu
     * @throws BadQueryStringException Pokud je parametr povinný a není přítomný
     */
    function get(string $key = null, bool $mandatory = true, $default = null);

    /**
     * Vrátí nahraný soubor
     *
     * @param $key string Klíč, pod kterým se má soubor nacházet
     *
     * @param bool $mandatory True, pokud je parametr povinný
     * @return FileEntry|null
     * @throws BadQueryStringException Pokud je parametr povinný a není přítomný
     */
    function getFile(string $key, bool $mandatory = true): FileEntry;

    /**
     * Vrátí pole nahraných souborů
     *
     * @return array
     */
    function getFiles();

    /**
     * Vrátí parametr na určitém indexu.
     * Pokud takový parametr neexistuje, vyhodí vyjímku.
     *
     * @param int $index Index parametru
     * @return mixed Obsah na indexu parametru
     * @throws BadQueryStringException Pokud index parametr neukazuje na žádný parametr
     */
    function getParam(int $index = -1);

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

    /**
     * Vrátí hlavičky obsazené v požadavku
     *
     * @return string[] Pole se všemi hlavičkami, které přišly spolu s požadavkem
     */
    function getHeaders();

    function spliceParams();
}