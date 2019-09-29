<?php


namespace app\model\http;

use Logger;

class FileEntry {

    /**
     * @var Logger
     */
    private $logger;

    private $__original;
    private $__pathinfo;

    public function __construct(array $file) {
      $this->logger = Logger::getLogger(__CLASS__);
      $this->__original = $file;
      $this->__pathinfo = pathinfo($this->getTmpName());
      $this->logger->info("New FileEntry was created.");
      $this->logger->trace($this->__original);
      $this->logger->trace($this->__pathinfo);
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
     * Zjistí, zda-li je nahraný soubor validní, nebo nastala při zpracování nějaká chyba
     *
     * @return bool True, pokud byl soubor úspěšně nahrán, jinak False
     */
    public function hasError(): bool {
        return $this->__original['error'] != 0;
    }

    /**
     * Vrátí velikost souboru
     *
     * @return int
     */
    public function getSize(): int {
        return $this->__original['size'];
    }

    /**
     * Vrátí zprávu o (ne)úspěšnosti zpracování/nahrání souboru na server
     *
     * @return string Zpráva o zpracování/nahrání souboru na server
     */
    public function getErrorMessage() {
        switch ($this->getError()) {
            case UPLOAD_ERR_OK:
                $message = "Soubor byl úspěšně nahrán na server.";
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $message = "Nahrávaný soubor přesahuje maximální povolenou velikost!";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "Soubor byl nahraný pouze částečně!";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "Žádný soubor nebyl nahrán!";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Chybí složka temp!";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Selhal zápis souboru na disk!";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "Nahrání souboru zrušil uživatel!";
                break;

            default:
                $message = "Nespecifikovaná chyba!";
                break;
        }
        return $message;
    }

    public function getExtension(): string {
        return $this->__pathinfo['extension'];
    }
}