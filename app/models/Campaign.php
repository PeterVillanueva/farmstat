<?php
/**
 * Campaign Model
 * PHP 8 Compatible
 */

class Campaign {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(int $limit = 12): array {
        $stmt = $this->db->prepare("SELECT * FROM campaigns ORDER BY created_at DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM campaigns WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int {
        $sql = "INSERT INTO campaigns (title, description, campaign_type, funding_goal, deadline, farmer_id, status) 
                VALUES (:title, :description, :campaign_type, :funding_goal, :deadline, :farmer_id, :status)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':campaign_type' => $data['campaign_type'],
            ':funding_goal' => $data['funding_goal'],
            ':deadline' => $data['deadline'] ?? null,
            ':farmer_id' => $data['farmer_id'] ?? null,
            ':status' => $data['status'] ?? 'active'
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
        
        $sql = "UPDATE campaigns SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function getActive(): array {
        $stmt = $this->db->query("SELECT * FROM campaigns WHERE status = 'active' ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function getTotalFunding(): float {
        $stmt = $this->db->query("SELECT SUM(amount_raised) as total FROM campaigns");
        $result = $stmt->fetch();
        return (float)($result['total'] ?? 0);
    }

    public function count(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM campaigns");
        return (int)$stmt->fetchColumn();
    }

    public function countActive(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM campaigns WHERE status = 'active'");
        return (int)$stmt->fetchColumn();
    }
}

