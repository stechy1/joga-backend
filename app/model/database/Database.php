<?php

namespace app\model\database;


use Logger;
use PDO;

/**
 * Class Database
 * Databázový wrapper pro pohodlnou práci s databází
 * @package app\model\database
 */
class Database implements IDatabase {

    /**
     * @var Logger
     */
    private $logger;
    /**
     * Databázové spojení
     * @var PDO
     */
    protected $connection;

    /**
     * Výchozí nastavení ovladače
     *
     * @var array
     */
    private static $settings = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", PDO::ATTR_EMULATE_PREPARES => false);

    /**
     * Database constructor.
     */
    public function __construct() {
        $this->logger = Logger::getLogger(__CLASS__);
    }

    /**
     * Připojí se k databázi pomocí daných údajů
     *
     * @param $host string Hostitelský stroj, kde běží databáze
     * @param $uzivatel string Jméno uživatele, pokd kterým se má aplikace připojit
     * @param $heslo string Heslo k databázi
     * @param $databaze string Název databáze, která se má použít
     */
    public function connect($host, $uzivatel, $heslo, $databaze) {
        if (isset($this->connection)) return;

        $this->connection = @new PDO("mysql:host=$host;dbname=$databaze", $uzivatel, $heslo, self::$settings);
    }

    /**
     * Spustí dotaz a vrátí z něj první řádek
     *
     * @param $query string Dotaz
     * @param array $parameters Parametry dotazu
     * @return array|null Asociativní pole obsahující data odpovídající záznamu, nebo null
     */
    public function queryOne($query, $parameters = array()) {
        $this->logger->trace($query);
        $result = $this->connection->prepare($query);
        $result->execute($parameters);
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Spustí dotaz a vrátí všechny jeho řádky jako pole asociativních polí
     *
     * @param $query string Dotaz
     * @param array $parameters Parametry dotazu
     * @return array|null Asociativní pole obsahující data odpovídající záznamu, nebo null
     */
    public function queryAll($query, $parameters = array()) {
        $this->logger->trace($query);
        $result = $this->connection->prepare($query);
        $result->execute($parameters);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Spustí dotaz a vrátí z něj první sloupec prvního řádku
     *
     * @param $query string Dotaz
     * @param array $parameters Parametry dotazu
     * @return mixed|null Vrátí první buňku z prvního sloupečku dotazu, nebo null, pokud dotazu nevyhověl žádný záznam
     */
    public function queryItself($query, $parameters = array()) {
        $result = $this->queryOne($query, $parameters);
        if ($result) {
            $result = array_values($result);
            return $result[0];
        }
        return null;
    }

    /**
     * Spustí dotaz a vrátí počet ovlivněných řádků
     *
     * @param $query string Dotaz
     * @param array $parameters Parametry dotazu
     * @return int Počet ovlivněných řádek
     */
    public function query($query, $parameters = array()) {
        $this->logger->trace($query);
        $result = $this->connection->prepare($query);
        $result->execute($parameters);
        return $result->rowCount();
    }

    /**
     * Vloží do tabulky nový řádek
     *
     * @param $table string Tabulka, s kterou se bude manipulovat
     * @param array $values Hodnoty, které se mají vložit
     * @return int ID vloženého záznamu
     */
    public function insert($table, $values = array()) {
        $this->query("INSERT INTO $table (" . implode(', ', array_keys($values)) . ") VALUES (" . str_repeat('?,', sizeOf($values) - 1) . "?)", array_values($values));
        return $this->connection->lastInsertId();
    }

    /**
     * Upraví záznam ve vybrané tabulce
     *
     * @param $table string Tabulka, s kterou se bude manipulovat
     * @param array $values Hodnoty, které se mají změnit
     * @param $condition string Podmínka
     * @param array $parameters Parametry podmínky
     * @return int Počet ovlivněných řádek
     */
    public function update($table, $values = array(), $condition, $parameters = array()) {
        return $this->query("UPDATE $table SET " . implode(' = ?, ', array_keys($values)) . " = ? " . $condition, array_merge(array_values($values), $parameters));
    }

    /**
     * Smaže záznam(y) z vybrané tabulky podle podmínky
     *
     * @param $table string Tabulka, s kterou se bude manipulovat
     * @param null $condition Podmínka
     * @param array $parameters Parametry
     * @return int Počet ovlivněných řádek
     */
    function delete($table, $condition = null, $parameters = array()) {
        $query = "DELETE FROM $table";
        if ($condition) $query .= ' ' . $condition;

        return $this->query($query, array_values($parameters));
    }

    /**
     * Započne novou transakci
     *
     * @return boolean True, pokud se podařilo založit novou transakci, jinak false
     */
    function beginTransaction() {
        return $this->connection->beginTransaction();
    }

    /**
     * Provede všechny změny
     *
     * @return boolean True, pokud se provedly všechny změny úspěšně, jinak false
     */
    function commit() {
        return $this->connection->commit();
    }

    /**
     * Vrátí zpět všechny změny co byly provedeny od posledního zavolání beginTransaction
     *
     * @return boolean True, pokud byly vráceny všechny změny úspěšně, jinak false
     */
    function rollback() {
        return $this->connection->rollBack();
    }

    /**
     * Vrací ID posledně vloženého záznamu
     *
     * @return mixed
     */
    public function getLastId() {
        return $this->connection->lastInsertId();
    }
}