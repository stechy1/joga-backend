<?php


namespace app\model\manager\email;


use app\model\exception\FileNotFoundException;
use Logger;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class EmailManager {

    const CONFIG_FILE = __APP_ROOT__ . "/config/email_config.php";

    const CONFIG_HOST = "host";
    const CONFIG_ENABLE_SMTP_AUTH = "enable_smtp_auth";
    const CONFIG_USERNAME = "username";
    const CONFIG_PASSWORD = "password";
    const CONFIG_PORT = "port";
    const CONFIG_NOREPLY = "noreply";

    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var PHPMailer
     */
    private $mailer;

    /**
     * @var string
     */
    private $myAddress;

    public function __construct() {
        $this->logger = Logger::getLogger(__CLASS__);
        // Instantiation and passing `true` enables exceptions
        $this->mailer = new PHPMailer(true);
        $this->setupMailer();
    }

    /***
     * Nastaví základní vlastnosti
     *
     * @throws FileNotFoundException Pokud není nalezen konfigurační soubor
     * @throws Exception
     */
    private function setupMailer() {
        if (!file_exists(self::CONFIG_FILE)) {
            throw new FileNotFoundException("Konfigurační soubor pro nastavení e-mailu není k dispozici!");
        }

        /** @noinspection PhpIncludeInspection */
        $config = require self::CONFIG_FILE;
        $this->myAddress = $config[self::CONFIG_USERNAME];
        $this->logger->trace($config);
        $this->mailer->isSMTP();
        $this->mailer->SMTPDebug = SMTP::DEBUG_OFF;
        $this->mailer->Host = $config[self::CONFIG_HOST];
        $this->mailer->Port = $config[self::CONFIG_PORT];
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $this->mailer->SMTPAuth = $config[self::CONFIG_ENABLE_SMTP_AUTH];
        $this->mailer->Username = $config[self::CONFIG_USERNAME];
        $this->mailer->Password = $config[self::CONFIG_PASSWORD];
        $this->mailer->CharSet = PHPMailer::CHARSET_UTF8;
        $this->mailer->setFrom($config[self::CONFIG_NOREPLY]);
        $this->mailer->setLanguage("cz");
    }

    /**
     * Odešle e-mail z kontaktního formuláře do schránky majitele stránek
     *
     * @param string $message Zpráva, kterou chce klient odeslat
     * @param string $name Jméno klienta
     * @param string $emailFrom E-mail, na který by měl majitel odpověďět
     * @throws Exception
     */
    public function sendEmailFromContactForm(string $message, string $name, string $emailFrom) {
        $this->mailer->setFrom($emailFrom);
        $this->mailer->addAddress($this->myAddress);
        $this->mailer->Subject = "Contact form - " . $name;
        $this->mailer->msgHTML($message);

        if (!$this->mailer->send()) {
            $this->logger->error("E-mail se nepodařilo odeslat!");
            $this->logger->error($this->mailer->ErrorInfo);
            throw new EmailException($this->mailer->ErrorInfo);
        }
    }

    /**
     * Odešle uvítací registrační e-mail s ověřovacím kódem.
     *
     * @param string $email
     * @param string $checkCode Ověřovací kód pro ověření e-mailové adresy
     * @throws Exception
     */
    public function sendRegisterEmail(string $email, string $checkCode) {
        $link = "{$_SERVER["HTTP_ORIGIN"]}/auth/check-code/{$checkCode}";
        $this->logger->debug("Link: " . $link);
        $this->mailer->addAddress($email);
        $this->mailer->Subject = "Ověření E-mailu";
        $this->mailer->msgHTML("Děkujeme, že jste se u nás zaregistrovali. \nPro ověření e-mailové adresy, prosím klikněte na následující odkaz: " . $link);

        if (!$this->mailer->send()) {
            $this->logger->error("Registrační e-mail se nepodařilo odeslat!");
            $this->logger->error($this->mailer->ErrorInfo);
            throw new EmailException($this->mailer->ErrorInfo);
        }
    }
}