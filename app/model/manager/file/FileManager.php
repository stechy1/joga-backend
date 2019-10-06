<?php

namespace app\model\manager\file;


use app\model\http\FileEntry;
use app\model\util\StringUtils;
use Logger;

/**
 * Class FileManager - Správce souborového systému
 * @package app\model\manager
 */
class FileManager {

    const FOLDER_UPLOADS = "uploads", FOLDER_USER_UPLOADS = "user", FOLDER_IMAGE = "image", FOLDER_LECTURES = "lectures", FOLDER_TMP = "tmp", FOLDER_INFO = "info", FOLDER_DOCUMENTS = "documents";

    private $logger;

    private $folderRoot;
    private $folders;

    function __construct() {
        $this->logger = Logger::getLogger(__CLASS__);
        $this->init();
    }

    /**
     * Spojí jednotlivé části cesty pomocí separátoru
     *
     * @param string $path Výchozí cesta
     * @param bool $separatorToEnd True, pokud chceš přidat na konec zmergované cesty separátor, jinak false
     * @param string ...$paths Další části cesty
     * @return string Výslednou spojenou cestu
     */
    public static function mergePath(string $path, bool $separatorToEnd = false, ...$paths) {
        if (StringUtils::endsWith($path, DIRECTORY_SEPARATOR)) {
            return $path . join("", $paths) . ($separatorToEnd) ? DIRECTORY_SEPARATOR : "";
        }
        return $path . DIRECTORY_SEPARATOR . join(DIRECTORY_SEPARATOR, $paths) . ($separatorToEnd) ? DIRECTORY_SEPARATOR : "";
    }

    /**
     * Inicializace instance
     */
    private function init() {
        $this->folderRoot = __PUBLIC_ROOT__ . DIRECTORY_SEPARATOR . "..";

        $this->folders[self::FOLDER_DOCUMENTS] = self::mergePath($this->folderRoot, false, "app", "documents", DIRECTORY_SEPARATOR); // $this->folderRoot ."/app/documents/";
        $this->folders[self::FOLDER_UPLOADS] = self::mergePath($this->folderRoot, false, "public", "uploads"); // $this->folderRoot . "public/uploads/";
        $this->folders[self::FOLDER_USER_UPLOADS] = self::mergePath($this->folders[self::FOLDER_UPLOADS], false,  "user");
        $this->folders[self::FOLDER_IMAGE] = self::mergePath($this->folders[self::FOLDER_UPLOADS], false,  "image");
        $this->folders[self::FOLDER_LECTURES] = self::mergePath($this->folders[self::FOLDER_UPLOADS], false,  "lectures");
        $this->folders[self::FOLDER_TMP] = self::mergePath($this->folders[self::FOLDER_UPLOADS], false,  "tmp");
        $this->folders[self::FOLDER_INFO] = self::mergePath($this->folders[self::FOLDER_UPLOADS], false,  "info");

        foreach ($this->folders as $folder) $this->createDirectory($folder);
    }

    /**
     * Metoda rekurzivně projede zadanou cestu a smaže všechno, co jí příjde do cesty
     *
     * @param $str string Cesta k souboru/složce
     * @return bool False, pokud není co smazat, jinak true
     */
    public function recursiveDelete($str) {
        if (is_file($str)) {
            return @unlink($str);
        } elseif (is_dir($str)) {
            $scan = glob(rtrim($str, '/') . '/*');
            foreach ($scan as $index => $path) {
                self::recursiveDelete($path);
            }
            return @rmdir($str);
        }

        return false;
    }

    /**
     * Metoda pro přesun souborů z jedné složky do druhé. Funguje rekurzivně
     *
     * @param $sourceDir string Zdrojová složky - odkud se mají soubory přesunout
     * @param $destDir string cílová složka - kam se mají soubory přesunout
     * @throws FileManipulationException Pokud zdroj nebo cíl není složka
     */
    public function moveFiles($sourceDir, $destDir) {
        if (!is_dir($sourceDir)) throw new FileManipulationException("Zdroj není složka");
        if (!is_dir($destDir)) throw new FileManipulationException("Cíl není složka");
        $fileArray = array_diff(scandir($sourceDir), array('..', '.'));
        foreach ($fileArray as $tmpFile) {
            if (is_dir($tmpFile)) self::moveFiles($tmpFile, $destDir . "/" . $tmpFile); else
                rename($sourceDir . "/" . $tmpFile, $destDir . $tmpFile);
        }
    }

    /**
     * Metoda pro přesun souborů z dočasné složky serveru do cílové
     *
     * @param $tmpFileName string Název souboru, který se má přesunout
     * @param $destDir string cílová složka - kam se mají soubory přesunout
     * @param $destFileName string Název souboru, do kterého se obsah přesune
     * @return string Plnou cestu k novému souboru
     * @throws FileManipulationException Pokud se přesun nepodaří
     */
    public function moveUploadedFiles($tmpFileName, $destDir, $destFileName): string {
        $destFile = $destDir . '/' . $destFileName;
        $this->logger->info("Přesouvám nahraný soubor: " . $tmpFileName . " do složky: " . $destFile);

        if (!move_uploaded_file($tmpFileName, $destFile)) {
            throw new FileManipulationException("Nepodařilo se přesunout soubor");
        }

        return $destFile;
    }

    /**
     * Metoda pro získání obsahu z adresáře
     *
     * @param $dir string Cesta k adresáři
     * @param string $prefixToRemove
     * @return array Pole souborů
     */
    public function getFilesFromDirectory(string $dir, string $prefixToRemove = "") {
        $files = array_diff(scandir($dir), array('..', '.'));
        $stats = [];

        foreach ($files as $file) {
            $workingFile = self::mergePath($dir, true, $file);
            $sha = sha1_file($workingFile);
            echo "Working file: " . $workingFile . "\n" . " Prefix to remove: " . $prefixToRemove;
            $publicPath = str_replace($prefixToRemove, "", $workingFile);
            echo "\n Result: " . $publicPath;
            $stats[$sha] = $publicPath;
        }

        return $stats;
    }

    /**
     * Pomocná metoda pro vytvoření složky, pokud neexistuje
     *
     * @param $path string Cesta ke složce
     * @param bool $throwException True, pokud se má při nezdaru vyhodit vyjímka
     * @throws FileManipulationException Pokud se nepodaří vytvořit složku
     */
    public function createDirectory(string $path, bool $throwException = false) {
        if (!file_exists($path)) {
            if (!mkdir($path, 0777, true)) {
                $this->logger->error("Nepodařilo se vytvořit složku: " . $path);
                if ($throwException) {
                    throw new FileManipulationException("Nepodařilo se vytvořit složku: " . $path);
                }
            }
        }
    }

    /**
     * Metoda pro získání cesty k zadané složce
     *
     * @param $name string Název složky
     * @return string Cestu k zadané složce
     * @throws FileManipulationException Pokud požadovaná složka neexistuje
     */
    public function getDirectory($name) {
        if (!array_key_exists($name, $this->folders)) throw new FileManipulationException('Požadovaná složka neexistuje');

        return $this->folders[$name];
    }

    /**
     * Vrátí hash souboru
     *
     * @param string $path Cesta k souboru
     * @return false|string Hash souboru, nebo false v případě, že se hash nepodařilo vytvořit
     */
    public function hashFile(string $path) {
        return sha1_file($path);
    }

    /**
     * Vytvoří nový soubor a zapíše do něj obsah. Pokud soubor existuje, obsah se přepíše
     *
     * @param $path string Cesta k souboru
     * @param $text string Obsah souboru
     * @throws FileManipulationException Pokud se zápis do souboru nezdaří
     */
    public function writeFile($path, $text) {
        $this->logger->trace("Zapisuji obsah do souboru: " . $path);
        if (!file_exists($path)) {
            if (!touch($path)) {
                throw new FileManipulationException("Soubor se nepodařilo vytvořit!");
            }
        }
        if (!file_put_contents($path, $text)) {
            throw new FileManipulationException("Obsah se nepodařilo zapsat do souboru!");
        }
    }

    /**
     * Přečte soubor a vrátí obsah
     *
     * @param $path string Cesta k souboru
     * @return string Obsah souboru
     */
    public function readFile($path) {
        return file_get_contents($path);
    }

    /**
     * Upraví cestu zeké na relativní
     *
     * @param string Statická cesta
     * @return string Relativná cestu
     */
    public function getRelativePath($path) {
        return str_replace($this->folderRoot, "/", $path);
    }

    /**
     * Odstraní soubor v parametru
     *
     * @param string $filePath Cesta k souboru, který se má odstranit
     * @throws FileManipulationException
     */
    public function deleteFile(string $filePath) {
        $this->logger->trace("Mažu soubor: " . $filePath);
        if (!unlink($filePath)) {
            throw new FileManipulationException("Nepodařilo se odstranit soubor: " . $filePath);
        }
    }

    /**
     * Přejmenuje soubor na nový název
     *
     * @param string $file Plná cesta k souboru
     * @param string $newName Nový název souboru (bez koncovky)
     * @return string Plnou cestu k nově přejmenovanému souboru
     * @throws FileManipulationException Pokud se přejmenování nepodaři
     */
    public function rename(string $file, string $newName) {
        $info = pathinfo($file);
        $this->logger->trace("Přejmenovávám v souboru: " . $file . " název souboru: " . $info['filename'] . " na: " . $newName);
        //                     Co hledám,       čím to nahradím, kde to hledám
        $newFile = str_replace($info['filename'], $newName, $file);
        $this->logger->trace("Nový název souboru: " . $newFile);
        if (!rename($file, $newFile)) {
            throw new FileManipulationException("Soubor: " . $file . " se nepodařilo přejmenovat na: " . $newFile);
        }

        return $newFile;
    }
}