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
    /**
     * @var bool
     */
    private $checked;

    public function __construct(int $id, string $email, int $role, bool $checked) {
        $this->id = $id;
        $this->email = $email;
        $this->role = $role;
        $this->checked = $checked;
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

    /**
     * @return bool
     */
    public function isChecked(): bool {
        return $this->checked;
    }

}