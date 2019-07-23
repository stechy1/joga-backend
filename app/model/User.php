<?php


namespace app\model;


class User {

    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $email;
    /**
     * @var string
     */
    private $password;
    /**
     * @var int
     */
    private $role;

    public function __construct(int $id, string $email, int $role, string $password = null) {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail(): string {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword(): string {
        return $this->password;
    }

    /**
     * @return int
     */
    public function getRole(): int {
        return $this->role;
    }

}