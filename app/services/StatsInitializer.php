<?php

class StatsInitializer
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function init(int $userId, array $profile): void
    {
        $userId = (int)$userId;

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
        $userId = (int)$userId;

        $result = mysqli_query($this->conn, "
            SELECT 1 FROM user_stats WHERE user_id = $userId LIMIT 1
        ");

        return ($result && mysqli_fetch_assoc($result)) ? true : false;
    }

    private function getAllStats(): array
    {
        $result = mysqli_query($this->conn, "SELECT code FROM stats");

        $stats = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $stats[] = $row;
        }

        return $stats;
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
        $userId = (int)$userId;
        $value  = max(0, min(100, (int)$value));

        $statCode = mysqli_real_escape_string($this->conn, $statCode);

        mysqli_query($this->conn, "
            INSERT INTO user_stats (user_id, stat_code, value)
            VALUES ($userId, '$statCode', $value)
        ");
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