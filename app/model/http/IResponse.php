<?php


namespace app\model\http;

interface IResponse {

    /**
     * Přidá data, která putujou ke klientovi
     *
     * @param string $key Klíč
     * @param mixed $value Hodnota
     * @param bool $jsonEncode False, pokud se nemají data enkodovat do jsonu, výchozi je true
     */
    public function addData(string $key, $value, $jsonEncode = true): void;

    public function addFlowData(string $key, $value): void;

    public function getFlowData(string $key);

    /**
     * Nastaví příslušný http kód
     *
     * @param int $code
     */
    public function setCode(int $code): void;

    /**
     * Nastaví header
     *
     * @param string $name Název hlavičky
     * @param string $value Hodnota v hlavičce
     */
    public function setHeader(string $name, string $value): void;

    public function getCode(): int;

    public function getData();
}