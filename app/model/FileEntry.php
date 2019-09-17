<?php


namespace app\model\http;


use Logger;

class FileEntry {

    private $logger;

    private $__original;
    private $__pathinfo;

    public function __construct(array $file) {
      $this->logger = Logger::getLogger(__CLASS__);
      $this->__original = $file;
      $this->__pathinfo = pathinfo($this->getTmpName());
    }

    /**
     * Vrátí název souboru
     *
     * @return string
     */
    public function getName(): string {
        return $this->__original['name'];
    }

    /**
     * Vrátí typ souboru
     *
     * @return string
     */
    public function getType(): string {
        return $this->__original['type'];
    }

    /**
     * Vrátí cestu k souboru v dočasně složce serveru
     *
     * @return string
     */
    public function getTmpName(): string {
        return $this->__original['tmp_name'];
    }

    /**
     * Vrátí číslo chyby, pokud nějaká nastala
     * 0 = bez chyby
     *
     * @return int
     */
    public function getError(): int {
        return $this->__original['error'];
    }

    /**
     * Vrátí velikost souboru
     *
     * @return int
     */
    public function getSize(): int {
        return $this->__original['size'];
    }

    private function codeToMessage($code) {
        switch ($code) {
            case UPLOAD_ERR_OK:
                $message = "There is no error, the file uploaded with success.";
                break;
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension";
                break;

            default:
                $message = "Unknown upload error";
                break;
        }
        return $message;
    }

    public function getExtension(): string {
        return $this->__pathinfo['extension'];
    }
}