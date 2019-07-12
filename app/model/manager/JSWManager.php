<?php


namespace app\model\manager;


use app\model\JWTModel;
use app\model\User;
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
        $issuer_claim = JSW_ISSUER;
        $issuedat_claim = time(); // issued at
        $notbefore_claim = $issuedat_claim + 10; //not before in seconds
        $expire_claim = $issuedat_claim + 60; // expire time in seconds
        $token = array(
            "iss" => $issuer_claim,    // A string containing the name or symbol of the institution application. are often a website name and may be accustomed discard tokens from alternative applications.
            "iat" => $issuedat_claim,  // timestamp of token supplying.
            "nbf" => $notbefore_claim, // It is the timestamp when we start token should and considering it valid. It should be equal to or greater than iat. In this case , after issuing the token, it will be valid for 10 seconds.
            "exp" => $expire_claim,    // Timestamp of once the token ought to stop to be valid. Ought to be larger than iat and nbf. During this case, the token can expire sixty seconds once being issued.
            "id" => $user->getId()
        );
        $privKey = $this->readPrivateKey();
        return JWT::encode($token, $privKey, self::JSW_ALGORITHM);
    }

    public function validateJSQ(string $jwt) {
        JWT::decode($jwt, $this->readPublicKey(), [self::JSW_ALGORITHM]);
    }
}