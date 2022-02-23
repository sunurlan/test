<?php
namespace MyProject\Models\Users;

use Vendor\Models\ActiveRecordEntity;
use MyProject\Exceptions\InvalidArgumentException;
use MyProject\Exceptions\ActivationException;

class User extends ActiveRecordEntity
{
    /** @var string */
    protected $nickname;

    /** @var string */
    protected $email;

    /** @var int */
    protected $isConfirmed;

    /** @var string */
    protected $role;

    /** @var string */
    protected $passwordHash;

    /** @var string */
    protected $authToken;

    /** @var string */
    protected $createdAt;

    /**
     * @return string
     */
    public function getNickname(): string {
        return $this->nickname;
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
    public function getIsConfirmed(): int {
        return (int) $this->isConfirmed;
    }

    /**
     * @return string
     */
    public function getAuthToken(): string {
        return $this->authToken;
    }
    
    /**
     * @return bool
     */
    public function isAdmin(): bool {
        return $this->role === 'admin';
    }

    public static function signUp(array $userData): User {
        if (empty($userData['nickname'])) {
            throw new InvalidArgumentException('Не передан nickname');
        }
        if (!preg_match('/^[a-z0-9A-Z]+$/', $userData['nickname'])) {
            throw new InvalidArgumentException('Nickname может состоять только из символов латинского алфавита и цифр');
        }
        if (empty($userData['email'])) {
            throw new InvalidArgumentException('Не передан email');
        }
        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Email некорректен');
        }
        if (empty($userData['password'])) {
            throw new InvalidArgumentException('Не передан пароль');
        }
        if (mb_strlen($userData['password']) < 8) {
            throw new InvalidArgumentException('Пароль должен быть не менее 8 символов');
        }
        if (static::findOneByColumn('nickname', $userData['nickname']) !== null) {
            throw new InvalidArgumentException('Пользователь с таким nickname уже существует');
        }
        if (static::findOneByColumn('email', $userData['email']) !== null) {
            throw new InvalidArgumentException('Пользователь с таким email уже существует');
        }
        $user = new User();
        $user->nickname = $userData['nickname'];
        $user->email = $userData['email'];
        $user->passwordHash = password_hash($userData['password'], PASSWORD_DEFAULT);
        $user->isConfirmed = false;
        $user->role = 'user';
        $user->authToken = sha1(random_bytes(100)) . sha1(random_bytes(100));
        $user->save();
        return $user;
    }
    public static function activate(int $userId, string $activationCode): User {
        $user = static::getById($userId);
        if ($user === null) {
            throw new ActivationException('Нет такого пользователя');
        }
        if ($user->isConfirmed == 1) {
            throw new ActivationException('Пользователь уже активирован');
        }
        $isCodeValid = UserActivationService::checkActivationCode($user, $activationCode);
        if ($isCodeValid === false) {
            throw new ActivationException('Код активации не верен');
        }
        $user->isConfirmed = true;
        $user->save();
        return $user;
    }
    public static function login(array $loginData): self {
        if (empty($loginData['email'])) {
            throw new InvalidArgumentException('Не передан email');
        }
        if (empty($loginData['password'])) {
            throw new InvalidArgumentException('Не передан password');
        }
        $user = User::findOneByColumn('email', $loginData['email']);
        if ($user === null) {
            throw new InvalidArgumentException('Нет пользователя с таким email');
        }
        if (!password_verify($loginData['password'], $user->getPasswordHash())) {
            throw new InvalidArgumentException('Неправильный пароль');
        }
        if (!$user->isConfirmed) {
            throw new InvalidArgumentException('Пользователь не подтверждён');
        }
        $user->refreshAuthToken();
        $user->save();
        return $user;
    }
    public function getPasswordHash(): string {
        return $this->passwordHash;
    }
    private function refreshAuthToken(): string {
        return $this->authToken = sha1(random_bytes(100)) . sha1(random_bytes(100));
    }
    protected static function getTableName(): string
    {
        return 'users';
    }
}