<?php

namespace App\Core;

/**
 * Auth helper untuk mengecek autentikasi dan otorisasi
 */
class Auth
{
    public static function isAuthenticated(): bool
    {
        return Session::has('user_id');
    }

    public static function requireLogin(): void
    {
        if (!self::isAuthenticated()) {
            header('Location: /login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
            exit;
        }
    }

    public static function requireRole(string|array $roles): void
    {
        self::requireLogin();
        $userRole = Session::get('role');
        $roles = (array) $roles;

        if (!in_array($userRole, $roles)) {
            http_response_code(403);
            die('Access Denied: You do not have permission to access this page.');
        }
    }

    public static function getUser(): array
    {
        return [
            'id' => Session::get('user_id'),
            'email' => Session::get('email'),
            'role' => Session::get('role'),
            'full_name' => Session::get('full_name'),
        ];
    }

    public static function getRole(): ?string
    {
        return Session::get('role');
    }

    public static function getId(): ?int
    {
        return Session::get('user_id');
    }
}
