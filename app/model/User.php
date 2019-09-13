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
     * @var int
     */
    private $role;

    public function __construct(int $id, string $email, int $role) {
        $this->id = $id;
        $this->email = $email;
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
     * @return int
     */
    public function getRole(): int {
        return $this->role;
    }

}