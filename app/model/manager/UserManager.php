<?php

namespace app\model\manager;

use app\model\database\Database;
use app\model\JWTModel;
use app\model\service\exception\UserException;
use app\model\User;
use Logger;
use PDOException;

/**
 * Class UserManager - Správce jednotlivých uživatelů
 * @Inject Database
 * @Inject JSWManager
 * @package app\model\manager
 */
class UserManager {

    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var Database
     */
    private $database;
    /**
     * @var JSWManager
     */
    private $jswmanager;

    /**
     * UserManager constructor.
     */
    public function __construct() {
        $this->logger = Logger::getLogger(__CLASS__);
    }

//    private function createJSW(User $user) {
//        //return Token::create($user->getId(), self::JSW_SECRET, time() + 3600, "localhost");
//    }

    /**
     * Zaregistruje nového uživatele
     *
     * @param $email
     * @param $password
     * @throws UserException () Pokud se registrace nezdaří
     */
    public function register($email, $password) {
        $pass = password_hash($password, PASSWORD_ARGON2I);
        $user = array('email' => $email, 'password' => $pass);
        try {
            $this->database->insert('users', $user);
        } catch (PDOException $chyba) {
            $this->logger->error($chyba);
            throw new UserException('Uživatel s touto e-mailovou adresou je již zaregistrovaný.');
        }
    }

    /**
     * Přihlásí uživatele do systému
     *
     * @param $email
     * @param $password
     * @return JWTModel
     * @throws UserException () Pokud se přihlášení nezdaří
     */
    public function login($email, $password): string {

//        Získání údajů
        $fromDb = $this->database->queryOne('
                        SELECT id, password
                        FROM users
                        WHERE email = ?
                ', [$email]);
        if (!$fromDb) throw new UserException('Špatné jméno nebo heslo.');

//        Ověření hesla
        $hash = $fromDb['password'];
        if (!password_verify($password, $hash)) {
            throw new UserException('Špatné jméno nebo heslo.');
        }

        return $this->jswmanager->createJSW(new User($fromDb['id'], $email, null));

//        return $this->createJSW(new User($fromDb['id'], $fromDb['email'], null));

    }

//    public function verifyJSW(string $token) {
//        return Token::validate($token, self::JSW_SECRET);
//    }
}