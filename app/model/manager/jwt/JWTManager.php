<?php


namespace app\model\manager\jwt;


use app\model\User;
use DateInterval;
use DateTime;
use Firebase\JWT\JWT;
use Logger;

class JWTManager {

    const
        JWT_ALGORITHM = "RS256",
        JWT = __APP_ROOT__ ."/config/private.key",
        JWT_PUBLIC_KEY_PATH = __PUBLIC_ROOT__ ."/public.key";

    /**
     * @var Logger
     */
    private $logger;

    private function readPrivateKey(): string {
        $this->logger->trace("Reading private key on the path: " . self::JWT);
        return file_get_contents(self::JWT);
    }

    private function readPublicKey(): string {
        $this->logger->trace("Reading private key on the path: " . self::JWT_PUBLIC_KEY_PATH);
        return file_get_contents(self::JWT_PUBLIC_KEY_PATH);
    }

    public function __construct() {
        $this->logger = Logger::getLogger(__CLASS__);
    }

    public function createJWT(User $user, bool $remember): string {
        $issuer_claim = JWT_ISSUER;
        $time = new DateTime();
        $issuedat_claim = $time->getTimestamp();
        if ($remember) {
            $time->add(new DateInterval("P7D"));
        } else {
            $time->add(new DateInterval("PT2H"));
        }
        $expire_claim = $time->getTimestamp();
        $token = array(
            "iss" => $issuer_claim,    // Název vlastníka tokenu (název aplikace)
            "iat" => $issuedat_claim,  // Timestamp času, kdy byl token vygenerován
            "exp" => $expire_claim,    // Timestamp času, kdy vyprší platnost tokenu
            "id" => $user->getId(),    // ID uživatele
            "role" => $user->getRole() // Role uživatele
        );
        $privKey = $this->readPrivateKey();
        return JWT::encode($token, $privKey, self::JWT_ALGORITHM);
    }

    /**
     * @param string $jwt
     * @return object
     */
    public function decodeJWT(string $jwt) {
        return JWT::decode($jwt, $this->readPublicKey(), [self::JWT_ALGORITHM]);
    }
}