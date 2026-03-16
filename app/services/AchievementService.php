<?php

class AchievementService
{
    private PDO $db;
    private int $userId;

    public function __construct(PDO $db, int $userId)
    {
        $this->db = $db;
        $this->userId = $userId;
    }

    public function checkAll(): void
{
    $stmt = $this->db->query("
        SELECT 
            a.id as achievement_id,
            ac.condition_type,
            ac.condition_value
        FROM achievements a
        JOIN achievement_conditions ac 
            ON ac.achievement_id = a.id
    ");

    $conditions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($conditions as $cond) {

        if ($this->checkCondition(
            $cond['condition_type'],
            (int)$cond['condition_value']
        )) {
            $this->unlock($cond['achievement_id']);
        }
    }
}

    private function checkCondition(string $type, int $value): bool
{
    switch ($type) {

        case 'food_count':

            $stmt = $this->db->prepare("
                SELECT COUNT(*) 
                FROM food_logs
                WHERE user_id = ?
            ");

            $stmt->execute([$this->userId]);

            return $stmt->fetchColumn() >= $value;


        case 'activity_count':

            $stmt = $this->db->prepare("
                SELECT COUNT(*) 
                FROM activity_logs
                WHERE user_id = ?
            ");

            $stmt->execute([$this->userId]);

            return $stmt->fetchColumn() >= $value;


        case 'level':

            $stmt = $this->db->prepare("
                SELECT level 
                FROM experience
                WHERE user_id = ?
            ");

            $stmt->execute([$this->userId]);

            return $stmt->fetchColumn() >= $value;


        case 'exp':

            $stmt = $this->db->prepare("
                SELECT exp 
                FROM experience
                WHERE user_id = ?
            ");

            $stmt->execute([$this->userId]);

            return $stmt->fetchColumn() >= $value;

    }

    return false;
}

    private function unlock(int $achievementId): void
{
    $stmt = $this->db->prepare("
        SELECT 1 
        FROM user_achievements
        WHERE user_id = ?
        AND achievement_id = ?
    ");

    $stmt->execute([$this->userId, $achievementId]);

    if ($stmt->fetch())
        return;

    $stmt = $this->db->prepare("
        INSERT INTO user_achievements
        (user_id, achievement_id)
        VALUES (?, ?)
    ");

    $stmt->execute([$this->userId, $achievementId]);
}
}