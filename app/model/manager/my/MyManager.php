<?php
namespace app\model\manager\my;


use app\model\manager\file\FileManager;
use app\model\manager\file\FileManipulationException;
use app\model\manager\file\InfoTypeConversionException;
use Exception;
use Logger;
use app\model\database\Database;


/**
 * Class MeService
 * @Inject Database
 * @Inject FileManager
 */
class MyManager {

    const TABLE_NAME = "informations";

    const INFO_TYPE_MY = "my";
    const INFO_TYPE_STUDIO = "studio";
    const INFO_TYPE_MY_VALUE = 0;
    const INFO_TYPE_STUDIO_VALUE = 1;

    const COLUMN_INFO_ID = 'id';
    const COLUMN_INFO_TYPE = 'type';
    const COLUMN_INFO_CONTENT = 'content';

    // File part
    const INFO_SUFFIX = ".txt";

    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var Database
     */
    private $database;
    /**
     * @var FileManager
     */
    private $filemanager;

    public function __construct() {
      $this->logger = Logger::getLogger(__CLASS__);
    }

    /**
     * Převede typ na jeho číselnou reprezentaci
     *
     * @param $what
     * @return int
     * @throws InfoTypeConversionException
     */
    private function typeToNumber($what) {
        $type = self::INFO_TYPE_MY_VALUE;
        if ($what == self::INFO_TYPE_MY) {
            $type = self::INFO_TYPE_MY_VALUE;
        } elseif ($what == self::INFO_TYPE_STUDIO) {
            $type = self::INFO_TYPE_STUDIO_VALUE;
        } else {
            throw new InfoTypeConversionException("Nerozeznaný typ obsahu");
        }

        return $type;
    }

    public function getInformations() {
        $result = $this->database->queryAll("SELECT id, type, content FROM informations");
        return [
            self::INFO_TYPE_MY => $result[0][self::COLUMN_INFO_CONTENT],
            self::INFO_TYPE_STUDIO => $result[1][self::COLUMN_INFO_CONTENT],
        ];
    }

    /**
     * ULoží obsah informaci do databáze
     *
     * @param string $what Typ informace "my", "studio"
     * @param string $content Obsah
     * @throws Exception Pokud se něco nepovede
     */
    public function save(string $what, string $content) {
        $type = $this->typeToNumber($what);
        $count = $this->database->update(self::TABLE_NAME, [self::COLUMN_INFO_CONTENT => $content], "WHERE type = ?", [$type]);
        if ($count != 1) {
            $this->logger->error("Nebyl nalezen řádek s informacemi, který by se měl aktualizovat");
            throw new Exception("Řádek v tabulce se nepodařilo aktualizovat.");
        }
    }

    /**
     * Publikuje vybraný obsah
     *
     * @param $what string Typ obsahu, který se má publikovat
     * @throws InfoTypeConversionException
     * @throws FileManipulationException
     */
    public function publish(string $what) {
        $type = $this->typeToNumber($what);
        $content = $this->database->queryItself("SELECT content FROM informations WHERE type = ?", [$type]);
        $dir = $this->filemanager->getDirectory(FileManager::FOLDER_INFO);
        $infoFile = FileManager::mergePath($dir, false, $what) . self::INFO_SUFFIX;
        $this->filemanager->writeFile($infoFile, $content);
    }

}