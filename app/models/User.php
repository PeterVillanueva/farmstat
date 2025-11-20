<?php
/**
 * User Model
 * PHP 8 Compatible
 */

class User {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findByEmail(string $email, ?string $role = null): ?array {
        $sql = "SELECT * FROM users WHERE email = :email";
        $params = [':email' => $email];
        
        if ($role !== null) {
            $sql .= " AND role = :role";
            $params[':role'] = $role;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $user = $stmt->fetch();
        
        return $user ?: null;
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int {
        $sql = "INSERT INTO users (name, email, password, role, created_at) 
                VALUES (:name, :email, :password, :role, NOW())";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':password' => password_hash($data['password'], PASSWORD_DEFAULT),
            ':role' => $data['role']
        ]);
        
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $fields = [];
        $params = [':id' => $id];
        
        foreach ($data as $key => $value) {
            if ($key !== 'id') {
                $fields[] = "{$key} = :{$key}";
                $params[":{$key}"] = $value;
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function updateLastLogin(int $id): bool {
        $stmt = $this->db->prepare("UPDATE users SET last_login = NOW(), login_count = login_count + 1 WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function getAll(int $limit = 50): array {
        $stmt = $this->db->prepare("SELECT id, name, email, role, status, created_at FROM users ORDER BY created_at DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function emailExists(string $email): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetchColumn() > 0;
    }
}

