<?php


namespace app\model\manager\carousel;


use app\model\database\Database;
use app\model\manager\file\FileManager;
use app\model\manager\file\FileManipulationException;
use app\model\http\FileEntry;
use Exception;
use Logger;

/**
 * Class CarouselManager
 * @Inject Database
 * @Inject FileManager
 * @package app\model\manager
 */
class CarouselManager {

    const TABLE_NAME = "carousel";

    const COLUMN_IMAGE_ID = "id";
    const COLUMN_IMAGE_NAME = "name";
    const COLUMN_IMAGE_DESCRIPTION = "description";
    const COLUMN_IMAGE = "image";
    const COLUMN_ENABLED = "enabled";
    const COLUMN_VIEW_ORDER = "view_order";

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
     * Vrátí z databáze všechny dostupné obrázky
     *
     * @param $enabled True, pro všechny aktivní obrázky, False pro všechny neaktivní obrázky, null pro všechny obecně
     * @return array|null
     */
    public function all(bool $enabled = null) {
        $query = "SELECT id, name, description, path, enabled, view_order FROM carousel";
        $params = [];
        if (isset($enabled)) {
            $query .= " WHERE enabled = ?";
            $params[] = $enabled ? 1 : 0;
        }
        return $this->database->queryAll($query, $params);
    }

    /**
     * Najde v databázi záznam o jednom obrázku a ten vrátí
     *
     * @param int $id ID obrázku
     * @return array Pole reprezentující data o obrázku
     * @throws ImageNotFoundException Pokud obrázek není nalezen
     */
    public function byId(int $id) {
        $fromDb = $this->database->queryOne("SELECT id, name, description, path, enabled, view_order FROM carousel WHERE id = ?", [$id]);

        if ($fromDb == null) {
            throw new ImageNotFoundException("Obrázek nebyl nalezen!");
        }

        return $fromDb;
    }

    /**
     * @param string $name Název obrázku
     * @param string $description Popis obrázku
     * @param FileEntry $image Instance reprezentující jeden obrázek
     * @return array Pole s hodnotami obrázku z databáze
     * @throws ImageUploadException Pokud se nepodaří vygenerovat záznam v databázi
     * @throws FileManipulationException Pokud se nepodaří přesunout soubor na své místo
     */
    public function addImage(string $name, string $description, FileEntry $image) {
        $this->database->beginTransaction();
        $result = [];
        $destFileName = "";
        $insertedID = -1;

        $result[self::COLUMN_IMAGE_NAME] = $name;
        $result[self::COLUMN_IMAGE_DESCRIPTION] = $description;
        $result[self::COLUMN_ENABLED] = 0;
        $result[self::COLUMN_VIEW_ORDER] = -1;

        // 1. Nakopíruj z tmp složky do public
        try {
            $imageDir = $this->filemanager->getDirectory(FileManager::FOLDER_IMAGE);
            $destFileName = $this->filemanager->moveUploadedFiles($image->getTmpName(), $imageDir, $image->getName());
        } catch (FileManipulationException $ex) {
            $this->logger->error($ex);
            throw new FileManipulationException($ex);
        }

        $fileHash = $this->filemanager->hashFile($destFileName);

//         2. Vlož záznam do databáze
        try {
            $insertedID = $this->database->insert(self::TABLE_NAME, [
                'name' => $name,
                'description' => $description,
                'path' => $fileHash,
            ]);
            $result['id'] = $insertedID;
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

        $newFileName = "";
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
            $this->database->update(self::TABLE_NAME, ['path' => $info['basename']], "WHERE id = ?", [$insertedID]);
            $result['path'] = $info['basename'];
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
        return $result;
    }

    /**
     * Smaže vybraný obrázek
     *
     * @param int $id ID obrázku, který se má smazat
     * @throws ImageProcessException Pokud se nepodaří smazat záznam z neznámého důvodu
     * @throws ImageNotFoundException Obrázek ke smazání nebyl nalezen
     */
    public function deleteImage(int $id) {
        try {
            $this->database->beginTransaction();
            $imageRecord = $this->byId($id);
            if ($imageRecord == null) {
                $this->logger->error("Obrázek ke smazání s ID: " . $id . " nebyl nalezen.");
                throw new ImageNotFoundException("Obrázek nebyl nalezen");
            }

            $deletedRows = $this->database->delete(self::TABLE_NAME, 'WHERE id = ?', [$id]);
            if ($deletedRows == 0) {
                $this->logger->error("Obrázek ke smazání s ID: " . $id . " nebyl nalezen.");
                throw new ImageNotFoundException("Obrázek nebyl nalezen");
            }

            $imageDir = $this->filemanager->getDirectory(FileManager::FOLDER_IMAGE);
            $this->filemanager->deleteFile($imageDir . '/' . $imageRecord['path']);

            $this->database->commit();
        } catch (FileManipulationException $ex) {
            $this->logger->error($ex->getMessage());
            $this->database->rollback();
        } catch (Exception $ex) {
            $this->database->rollback();
            throw new ImageProcessException($ex);
        }
    }

    /**
     * Aktualizuje základní údaje obrázku
     * Pro aktualizaci samotného obrázku, je třeba volat jinou funkci
     *
     * @param string $imageId ID obrázku, který se bude aktualizovat
     * @param string $name Nový název obrázku
     * @param string $description Nový popis obrázku
     * @param int $enabled 1 = aktivní, 0 = neaktivní
     * @param int $viewOrder Pořadí, ve kterém se obrázek zobrazí, nebo -1
     * @throws ImageProcessException Pokud se aktualizace údajů obrázku nezdaří
     */
    public function updateImage(string $imageId, string $name, string $description, int $enabled, int $viewOrder) {
        $updatedRows = $this->database->update(
            self::TABLE_NAME,
            [self::COLUMN_IMAGE_NAME => $name, self::COLUMN_IMAGE_DESCRIPTION => $description, self::COLUMN_ENABLED => $enabled, self::COLUMN_VIEW_ORDER => $viewOrder],
            "WHERE id = ?",
            [$imageId]
        );

        if ($updatedRows == 0) {
            $this->logger->error("Žádný obrázek nebyl aktualizován.");
            throw new ImageProcessException("Žádný obrázek nebyl aktualizován.");
        }
    }
}