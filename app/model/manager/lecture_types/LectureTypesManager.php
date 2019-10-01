<?php


namespace app\model\manager\lecture_types;


use app\model\database\Database;
use app\model\http\FileEntry;
use app\model\manager\carousel\ImageUploadException;
use app\model\manager\file\FileManager;
use app\model\manager\file\FileManipulationException;
use app\model\manager\lectures\LectureDataException;
use Exception;
use Logger;

/**
 * Class LectureTypesManager
 * @Inject Database
 * @Inject FileManager
 * @package app\model\manager
 */
class LectureTypesManager {

    const TABLE_NAME = "lecture_type";

    const COLUMN_ID = "id";
    const COLUMN_NAME = "name";
    const COLUMN_DESCRIPTION = "description";
    const COLUMN_PRICE = "price";
    const COLUMN_PATH = "path";
    const VIRAUAL_COLUMN_IMAGE = "image";

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

    public function all() {
        return $this->database->queryAll(
            "SELECT id, name, description, price, path
                    FROM lecture_type");
    }

    /**
     * Najde lekci podle Id
     *
     * @param int $lectureTypeId Id lekce, která se má najít
     * @return array
     * @throws LectureTypeException Pokud se nepodaří najít lekci se zadaným Id
     */
    public function byId(int $lectureTypeId) {
        $fromDb = $this->database->queryOne(
            "SELECT id, name, description, price, path
                    FROM lecture_type 
                    WHERE id = ?",
            [$lectureTypeId]);

        if ($fromDb == null) {
            throw new LectureTypeException("Typ lekce s Id ${lectureTypeId} nebyl nalezen!");
        }

        return $fromDb;
    }

    /**
     * Vloží nový typ lekce do databáze
     *
     * @param string $name
     * @param string $description
     * @param int $price
     * @param FileEntry $image
     * @return int Id nově založeného typu lekce
     * @throws FileManipulationException
     * @throws ImageUploadException
     */
    public function insert(string $name, string $description, int $price, FileEntry $image) {
        if ($image->hasError()) {
            throw new ImageUploadException($image->getErrorMessage());
        }

        $this->database->beginTransaction();

        $id = null;
        $destFileName = null;
        $insertedID = null;

        // 1. Nakopíruj z tmp složky do public
        try {
            $imageDir = $this->filemanager->getDirectory(FileManager::FOLDER_LECTURES);
            $destFileName = $this->filemanager->moveUploadedFiles($image->getTmpName(), $imageDir, $image->getName());
        } catch (FileManipulationException $ex) {
            $this->logger->error($ex);
            throw new FileManipulationException($ex);
        }

        $fileHash = $this->filemanager->hashFile($destFileName);

        // 2. Vlož záznam do databáze
        try {
            $id = $this->database->insert(self::TABLE_NAME, [
                self::COLUMN_NAME => $name,
                self::COLUMN_DESCRIPTION => $description,
                self::COLUMN_PRICE => $price,
                self::COLUMN_PATH => $fileHash,
            ]);
        } catch (Exception $ex) {
            $this->database->rollback();
            try {
                $this->filemanager->deleteFile($destFileName);
            } catch (FileManipulationException $ex) {
                $this->logger->error($ex);
            }
            $this->logger->error($ex);
            throw new ImageUploadException("Záznam o obrázku se nepodařilo vložit do databáze");
        }

        $newFileName = null;
        // 3. Přejmenuj název souboru ve veřejné složce za hash
        try {
            $newFileName = $this->filemanager->rename($destFileName, $fileHash);
        } catch (FileManipulationException $ex) {
            $this->logger->error($ex);
            $this->database->rollback();
            throw new FileManipulationException($ex);
        }

        // 4. Přejmenuj cestu k souboru v databázi
        $info = pathinfo($newFileName);
        try {
            $this->database->update(self::TABLE_NAME, ['path' => $info['basename']], "WHERE id = ?", [$id]);
        } catch (Exception $ex) {
            $this->logger->error($ex);
            try {
                $this->filemanager->deleteFile($destFileName);
            } catch (FileManipulationException $ex) {
                $this->logger->error($ex);
            }
            throw new ImageUploadException("Cesta v záznamu o obrázku se nepodařilo přejmenovat");
        }

        $this->database->commit();
        return $id;
    }

    /**
     * Aktualizuje údaje o typu lekce
     *
     * @param int $lectureTypeId Id typu lekce
     * @param string $name Název typu lekce
     * @param string $description Popis typu lekce
     * @param int $price Cena lekce
     * @throws LectureTypeDataException Pokud se údaje o lekci nepodaří aktualizovat
     */
    public function update(int $lectureTypeId, string $name, string $description, int $price) {
        $updated = $this->database->update(self::TABLE_NAME,
            [
            LectureTypesManager::COLUMN_NAME => $name,
            LectureTypesManager::COLUMN_DESCRIPTION => $description,
            LectureTypesManager::COLUMN_PRICE => $price
            ],
            " WHERE id = ?",
            [$lectureTypeId]);

        if ($updated == 0) {
            throw new LectureTypeDataException("Údaje o lekci se nepodařilo aktualizovat!");
        }
    }

    /**
     * Smaže vybraný typ lekce
     *
     * @param int $lectureTypeId Id lekce, která se má smazat
     * @throws LectureDataException Pokud se lekci nepodaří smazat
     */
    public function delete(int $lectureTypeId) {
        // TODO lekci pouze označím za smazanou, ale fyzicky v DB zůstane
        $deleted = $this->database->delete(self::TABLE_NAME, "WHERE id = ?", [$lectureTypeId]);

        if ($deleted == 0) {
            throw new LectureDataException("Lekci se nepodařilo smazat!");
        }
    }
}