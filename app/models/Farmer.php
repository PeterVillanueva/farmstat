<?php
/**
 * Farmer Model
 * PHP 8 Compatible
 */

class Farmer {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(int $limit = 20): array {
        $stmt = $this->db->prepare("SELECT * FROM farmers ORDER BY created_at DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM farmers WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int {
        $sql = "INSERT INTO farmers (full_name, years_experience, farm_location, farm_size, farming_method, land_ownership, varieties) 
                VALUES (:full_name, :years_experience, :farm_location, :farm_size, :farming_method, :land_ownership, :varieties)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':full_name' => $data['full_name'],
            ':years_experience' => $data['years_experience'] ?? 0,
            ':farm_location' => $data['farm_location'] ?? null,
            ':farm_size' => $data['farm_size'] ?? null,
            ':farming_method' => $data['farming_method'] ?? null,
            ':land_ownership' => $data['land_ownership'] ?? null,
            ':varieties' => $data['varieties'] ?? null
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
        
        $sql = "UPDATE farmers SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM farmers WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function count(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM farmers");
        return (int)$stmt->fetchColumn();
    }
}

