<?php 
namespace MyProject\Models\Users;

class UserAuthService 
{
    public static function createToken(User $user): void {
        $token = $user->getId() . ':' . $user->getAuthToken();
        setcookie('token', $token, 0, '/', '', false, true);
    }
    public static function getUserByToken(): ?User {
        $token = $_COOKIE['token'] ?? '';
        if (empty($token)) {
            return null;
        }
        [$userId, $authToken] = explode(':', $token, 2);
        $user = User::getById((int) $userId);
        if ($user === null) {
            return null;
        }
        if ($user->getAuthToken() !== $authToken) {
            return null;
        }
        return $user;
    }
    public static function deleteToken(): void {
        setcookie('token', '', -1, '/', '', false, true);
    }
}