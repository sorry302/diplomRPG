<?php

class AchievementService
{
    private $conn;
    private int $userId;

    public function __construct($conn, int $userId)
    {
        $this->conn = $conn;
        $this->userId = (int)$userId;
    }

    public function checkAll(): void
    {
        $result = mysqli_query($this->conn, "
            SELECT 
                a.id as achievement_id,
                ac.condition_type,
                ac.condition_value
            FROM achievements a
            JOIN achievement_conditions ac 
                ON ac.achievement_id = a.id
        ");

        $achievements = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $achievements[$row['achievement_id']][] = [
                'type' => $row['condition_type'],
                'value' => (int)$row['condition_value']
            ];
        }

        foreach ($achievements as $achievementId => $conditions) {
            $allMet = true;

            foreach ($conditions as $cond) {
                if (!$this->checkCondition($cond['type'], $cond['value'])) {
                    $allMet = false;
                    break;
                }
            }

            if ($allMet) {
                $this->unlock((int)$achievementId);
            }
        }
    }

    private function checkCondition(string $type, int $value): bool
    {
        $userId = $this->userId;

        switch ($type) {

            case 'food_count':
            case 'eat_count':

                $result = mysqli_query($this->conn, "
                    SELECT SUM(portions) as total
                    FROM food_logs
                    WHERE user_id = $userId
                ");

                $row = mysqli_fetch_assoc($result);
                return (int)$row['total'] >= $value;


            case 'activity_count':

                $result = mysqli_query($this->conn, "
                    SELECT SUM(quantity) as total
                    FROM activity_logs
                    WHERE user_id = $userId
                ");

                $row = mysqli_fetch_assoc($result);
                return (int)$row['total'] >= $value;


            case 'level':
            case 'level_reach':

                $result = mysqli_query($this->conn, "
                    SELECT level 
                    FROM experience
                    WHERE user_id = $userId
                ");

                $row = mysqli_fetch_assoc($result);
                return (int)$row['level'] >= $value;


            case 'exp':

                $result = mysqli_query($this->conn, "
                    SELECT exp 
                    FROM experience
                    WHERE user_id = $userId
                ");

                $row = mysqli_fetch_assoc($result);
                return (int)$row['exp'] >= $value;


            case 'stat_physical':
            case 'stat_intellect':
            case 'stat_spiritual':

                $statCode = str_replace('stat_', '', $type);
                $statCode = mysqli_real_escape_string($this->conn, $statCode);

                $result = mysqli_query($this->conn, "
                    SELECT value 
                    FROM user_stats
                    WHERE user_id = $userId AND stat_code = '$statCode'
                ");

                $row = mysqli_fetch_assoc($result);
                return (int)($row['value'] ?? 0) >= $value;
        }

        return false;
    }

 private function unlock(int $achievementId): void
{
    $userId = $this->userId;
    $achievementId = (int)$achievementId;

    $result = mysqli_query($this->conn, "
        SELECT 1 
        FROM user_achievements
        WHERE user_id = $userId
        AND achievement_id = $achievementId
    ");

    if ($result && mysqli_fetch_assoc($result)) {
        return;
    }

    // получаем инфу об ачивке
    $ach = mysqli_query($this->conn, "
        SELECT title, icon 
        FROM achievements 
        WHERE id = $achievementId
    ");

    $achievement = mysqli_fetch_assoc($ach);

    mysqli_query($this->conn, "
        INSERT INTO user_achievements (user_id, achievement_id)
        VALUES ($userId, $achievementId)
    ");

    //вывод уведомления
    $message = $achievement['icon'] . " Достижение получено: " . $achievement['title'];

    $this->addNotification($message, 'achievement');
}
private function addNotification($message, $type = 'achievement')
{
    $userId = $this->userId;

    mysqli_query($this->conn, "
        INSERT INTO notifications (user_id, type, message)
        VALUES ($userId, '$type', '$message')
    ");
}
}