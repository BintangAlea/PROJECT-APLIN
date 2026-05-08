<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class UsersModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM users ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    public function findByRole(string $role): array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE role = :role AND status = "active"');
        $stmt->execute([':role' => $role]);
        return $stmt->fetchAll();
    }

    public function register(string $email, string $password, string $fullName, string $phone, string $role): bool
    {
        // Check if email already exists
        if ($this->findByEmail($email)) {
            return false;
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare(
            'INSERT INTO users (email, password, full_name, phone, role) 
             VALUES (:email, :password, :full_name, :phone, :role)'
        );

        return $stmt->execute([
            ':email' => $email,
            ':password' => $hashedPassword,
            ':full_name' => $fullName,
            ':phone' => $phone,
            ':role' => $role,
        ]);
    }

    public function login(string $email, string $password): array|false
    {
        $user = $this->findByEmail($email);

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['password'])) {
            return false;
        }

        if ($user['status'] !== 'active') {
            return false;
        }

        return $user;
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $values = [':id' => $id];

        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $values[":$key"] = $value;
        }

        $stmt = $this->db->prepare(
            'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = :id'
        );

        return $stmt->execute($values);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function getTotalByRole(string $role): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) as count FROM users WHERE role = :role');
        $stmt->execute([':role' => $role]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
}
