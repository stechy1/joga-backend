<?php


namespace app\model\manager;


use app\model\User;
use DateInterval;
use DateTime;
use Firebase\JWT\JWT;
use Logger;

class JSWManager {

    const
        JSW_ALGORITHM = "RS256",
        JSW_PRIVATE_KEY_PATH = __APP_ROOT__ ."/config/private.key",
        JSW_PUBLIC_KEY_PATH = __APP_ROOT__ ."../public/public.key";

    /**
     * @var Logger
     */
    private $logger;

    private function readPrivateKey(): string {
        return file_get_contents(self::JSW_PRIVATE_KEY_PATH);
    }

    private function readPublicKey(): string {
        return file_get_contents(self::JSW_PUBLIC_KEY_PATH);
    }

    public function __construct() {
        $this->logger = Logger::getLogger(__CLASS__);
    }

    public function createJSW(User $user): string {
        $issuer_claim = JWT_ISSUER;
        $time = new DateTime();
        $issuedat_claim = $time->getTimestamp() * 1000;
        $time->add(new DateInterval("PT2H"));
        $expire_claim = $time->getTimestamp() * 1000;
        $token = array(
            "iss" => $issuer_claim,    // Název vlastníka tokenu (název aplikace)
            "iat" => $issuedat_claim,  // Timestamp času, kdy byl token vygenerován
            "exp" => $expire_claim,    // Timestamp času, kdy vyprší platnost tokenu
            "id" => $user->getId(),    // ID uživatele
            "role" => $user->getRole() // Role uživatele
        );
        $privKey = $this->readPrivateKey();
        return JWT::encode($token, $privKey, self::JSW_ALGORITHM);
    }

    public function validateJSQ(string $jwt) {
        JWT::decode($jwt, $this->readPublicKey(), [self::JSW_ALGORITHM]);
    }
}