<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class UsersModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM users');
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE user_id = :id');
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
        $stmt = $this->db->prepare('SELECT * FROM users WHERE ROLE = :role');
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
            'INSERT INTO users (NAME, email, PASSWORD, ROLE, loyalty_stage, total_spent, reward_points) 
             VALUES (:name, :email, :password, :role, :loyalty_stage, :total_spent, :reward_points)'
        );

        return $stmt->execute([
            ':name' => $fullName,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':role' => $role,
            ':loyalty_stage' => 1,
            ':total_spent' => 0,
            ':reward_points' => 0,
        ]);
    }

    public function login(string $email, string $password): array|false
    {
        $user = $this->findByEmail($email);

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['PASSWORD'])) {
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
            'UPDATE users SET ' . implode(', ', $fields) . ' WHERE user_id = :id'
        );

        return $stmt->execute($values);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE user_id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function getTotalByRole(string $role): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) as count FROM users WHERE ROLE = :role');
        $stmt->execute([':role' => $role]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
}
