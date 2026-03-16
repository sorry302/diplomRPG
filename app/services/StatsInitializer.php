<?php

class StatsInitializer
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function init(int $userId, array $profile): void
    {
        if ($this->hasStats($userId)) {
            return;
        }

        $stats = $this->getAllStats();

        foreach ($stats as $stat) {
            $value = $this->calculateStat($stat['code'], $profile);
            $this->insertUserStat($userId, $stat['code'], $value);
        }
    }

    private function hasStats(int $userId): bool
    {
        $stmt = $this->db->prepare(
            "SELECT 1 FROM user_stats WHERE user_id = ? LIMIT 1"
        );
        $stmt->execute([$userId]);
        return (bool) $stmt->fetchColumn();
    }

    private function getAllStats(): array
    {
        $stmt = $this->db->query("SELECT code FROM stats");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function calculateStat(string $code, array $profile): int
    {
        return match ($code) {
            'health'    => $this->calculateHealth($profile),
            'energy'    => 100,
            'fat'       => $this->calculateFat($profile),
            'physical'  => $this->calculatePhysical($profile),
            'intellect' => 50,
            'spiritual' => 0,
            default     => 0
        };
    }

    private function insertUserStat(int $userId, string $statCode, int $value): void
    {
        $stmt = $this->db->prepare(
            "INSERT INTO user_stats (user_id, stat_code, value)
             VALUES (:user_id, :stat_code, :value)"
        );
        $stmt->execute([
            ':user_id'   => $userId,
            ':stat_code'=> $statCode,
            ':value'    => max(0, min(100, $value)) // защита диапазона
        ]);
    }

    /* ===== ФОРМУЛЫ ===== */

    private function calculateHealth(array $profile): int
    {
        return max(50, 120 - (int)($profile['age'] * 0.5));
    }

    private function calculateFat(array $profile): int
    {
        $heightM = $profile['height'] / 100;
        $bmi = $profile['weight'] / ($heightM * $heightM);
        return (int) round($bmi * 2);
    }

    private function calculatePhysical(array $profile): int
    {
        $fat = $this->calculateFat($profile);

        $base = 100;
        $agePenalty = (int)($profile['age'] * 0.7);
        $fatPenalty = (int)($fat * 0.5);

        return max(30, $base - $agePenalty - $fatPenalty);
    }
}
