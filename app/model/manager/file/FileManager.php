<?php

namespace app\model\manager\file;


use app\model\http\FileEntry;
use Logger;

/**
 * Class FileManager - Správce souborového systému
 * @package app\model\manager
 */
class FileManager {

    const FOLDER_UPLOADS = "uploads", FOLDER_IMAGE = "image", FOLDER_LECTURES = "lectures", FOLDER_TMP = "tmp", FOLDER_INFO = "info";

    private $logger;

    private $folderRoot;
    private $folders;

    function __construct() {
        $this->logger = Logger::getLogger(__CLASS__);
        $this->init();
    }

    /**
     * Inicializace instance
     */
    private function init() {
        $this->folderRoot = $_SERVER['DOCUMENT_ROOT'] . '/';

        $this->folders[self::FOLDER_UPLOADS] = $this->folderRoot . "public/uploads/";
//        $this->folders[self::FOLDER_CATEGORY] = $this->folders['uploads'] . "category/";
        $this->folders[self::FOLDER_IMAGE] = $this->folders[self::FOLDER_UPLOADS] . "image/";
        $this->folders[self::FOLDER_LECTURES] = $this->folders[self::FOLDER_UPLOADS] . "lectures/";
        $this->folders[self::FOLDER_TMP] = $this->folders[self::FOLDER_UPLOADS] . "tmp/";
        $this->folders[self::FOLDER_INFO] = $this->folders[self::FOLDER_UPLOADS] . "info/";
//        $this->folders[self::FOLDER_FORUM_IMAGE] = $this->folders['image'] . "/forum";

        foreach ($this->folders as $folder) $this->createDirectory($folder);
    }

//    /**
//     * Vytvoří složku attachments v adresáři s článkem
//     *
//     * @param $artFolder string složka s článkem
//     * @return string Cestu ke složce attachment pro zadaný článek
//     */
//    public function getAttachmentsFolder($artFolder) {
//        $path = $artFolder . "/" . self::FOLDER_ATTACHMENT;
//        if (!file_exists($path)) mkdir($path);
//
//        return $path;
//    }

    public static function mergePath($path, ...$paths) {
        return $path . join("/", $paths);
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
     * @return array Pole souborů
     */
    public function getFilesFromDirectory($dir) {
        return array_diff(scandir($dir), array('..', '.'));
    }

    /**
     * Pomocná metoda pro vytvoření složky, pokud neexistuje
     *
     * @param $path string Cesta ke složce
     */
    private function createDirectory($path) {
        if (!file_exists($path)) {
            if (!mkdir($path, 0777, true)) {
                $this->logger->error("Nepodařilo se vytvořit složku: " . $path);
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

//    /**
//     * Vytvoří novou složku pro článek
//     *
//     * @param $categoryURL string URL adresa kategorie článku
//     * @param $articleURL string URL adresa článku
//     * @return string Cestu k složce s článkem
//     */
//    public function createArticleDirectory($categoryURL, $articleURL) {
//        $path = $this->folders[self::FOLDER_CATEGORY] . $categoryURL . "/" . $articleURL;
//        $this->createDirectory($path);
//
//        return $path;
//    }
    public function hashFile(string $path) {
        return sha1_file($path);
    }
//    /**
//     * Přečte soubor a vrátí jeho obsah v textové podobě
//     *
//     * @param $categoryURL string URL adresa kategorie článku (složka kategorie, ve které se článek nachází)
//     * @param $articleURL string URL adresa článku (složka článku, ve které se článek nachází)
//     * @return string Obsah souboru
//     * @throws FileManipulationException Pokud článek není nalezen
//     */
//    public function getArticleContent($categoryURL, $articleURL) {
//        $path = $this->folders[self::FOLDER_CATEGORY] . $categoryURL . "/" . $articleURL . "/" . $articleURL . '.markdown';
//        if (!file_exists($path)) throw new FileManipulationException("Požadovaný soubor nebyl nalezen");
//
//        return $this->readFile($path);
//    }

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
//        $file = fopen($path, "w");
//        fwrite($file, $text);
//        fclose($file);
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

//    /**
//     * Vytvoří dočasnou složku pro uživatele
//     *
//     * Pokud složka již existuje, tak smaže její obsah
//     */
//    public function createTmpDirectory() {
//        $tmpDirectory = $this->getTmpDirectory();
//        if (file_exists($tmpDirectory)) $this->clearTmpDirectory($tmpDirectory); else
//            $this->createDirectory($tmpDirectory);
//    }
//
//    /**
//     * @return string Vrátí cestu k dočasné složce uživatele.
//     */
//    public function getTmpDirectory() {
//        $dir = $this->folders[self::FOLDER_TMP] . $_SESSION['user']['id'] . "/";
//        if (!file_exists($dir)) $this->createDirectory($dir);
//
//        return $dir;
//    }

//    /**
//     * Metoda vyčistí junk-files z dočasné složky uživatele
//     *
//     * @param $tmpDirectory string Cesta k dočasné složce uživatele
//     */
//    public function clearTmpDirectory($tmpDirectory = null) {
//        $tmpDirectory = $tmpDirectory | $this->getTmpDirectory();
//        $this->recursiveDelete($tmpDirectory);
//    }

    /**
     * Upraví cestu zeké na relativní
     *
     * @param Path string Statická cesta
     * @return string Relativná cestu
     */
    public function getRelativePath($Path) {
        return str_replace($this->folderRoot, "/", $Path);
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