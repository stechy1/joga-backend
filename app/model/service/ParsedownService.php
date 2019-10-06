<?php


namespace app\model\service;


use app\model\util\MyParsedown;
use Logger;
use ParsedownExtra;

class ParsedownService {

    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var MyParsedown
     */
    private $parsedown;

    public function __construct() {
        $this->logger = Logger::getLogger(__CLASS__);
        $this->parsedown = new MyParsedown();
        $this->setBreaksEnabled(true);
        $this->setMarkupEscaped(false);
        $this->setUrlsLinked(true);
    }

    /**
     * Encoduje celý dokument na HTML text
     * Vše je uzavřeno do paragrafu <p>obsah</p>
     *
     * @param string $text Vstupní text
     * @return string Výstupní HTML dokument
     */
    public function encode(string $text): string {
        return $this->parsedown->text($text);
    }

    /**
     * Encoduje jednu řádku dokumentu
     *
     * @param string $text Vstupní text
     * @return string Výstupní řádka HTML dokumentu
     */
    public function encodeLine(string $text): string {
        return $this->parsedown->line($text);
    }

    /**
     * Povolí automatické odřádkování
     *
     * \n => <br/>
     *
     * @param bool $breaksEnabled
     */
    function setBreaksEnabled(bool $breaksEnabled) {
        $this->parsedown->setBreaksEnabled($breaksEnabled);
    }

    /**
     * Povolení excapování HTML značek ve vstupu
     * Nastav na false v případě vstupu od uživatele
     *
     * <div><strong>*Some text*</strong></div>
     * Output: <p>&lt;div>&lt;strong><em>Some text</em>&lts;/strong>&lt;/div></p>
     *
     * @param bool $markupEscaped
     */
    function setMarkupEscaped(bool $markupEscaped) {
        $this->parsedown->setMarkupEscaped($markupEscaped);
    }

    /**
     * Povolení automatické transformace linků na odkaz
     *
     * You can find Parsedown at http://parsedown.org
     * Output: <p>You can find Parsedown at http://parsedown.org</p>
     *
     * @param bool $urlsLinked
     */
    function setUrlsLinked(bool $urlsLinked) {
        $this->parsedown->setUrlsLinked($urlsLinked);
    }

    function setSafeMode($safeMode) {

    }

    function setStrictMode($strictMode) {

    }
}