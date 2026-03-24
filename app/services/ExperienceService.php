<?php

class ExperienceService
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function addExp(int $userId, int $amount): void
    {
        $userId = (int)$userId;
        $amount = (int)$amount;

        // Добавляем опыт
        mysqli_query($this->conn, "
            UPDATE experience
            SET exp = exp + $amount
            WHERE user_id = $userId
        ");

        // Проверяем ап уровня
        $this->checkLevelUp($userId);
    }

    private function checkLevelUp(int $userId): void
    {
        $userId = (int)$userId;

        // Текущий опыт и уровень
        $result = mysqli_query($this->conn, " SELECT exp, level FROM experience WHERE user_id = $userId
        ");

        $expData = mysqli_fetch_assoc($result);

        if (!$expData) {
            return;
        }

        $currentExp   = (int)$expData['exp'];
        $currentLevel = (int)$expData['level'];

        // Сколько нужно XP для следующего уровня
        $result = mysqli_query($this->conn, "SELECT required_exp FROM levels WHERE level = " . ($currentLevel + 1));

        $nextLevel = mysqli_fetch_assoc($result);

        if (!$nextLevel) {
            return; 
        }

        if ($currentExp >= (int)$nextLevel['required_exp']) {
            $this->levelUp($userId, $currentLevel + 1);
        }
    }

    private function levelUp(int $userId, int $newLevel): void
    {
        $userId   = (int)$userId;
        $newLevel = (int)$newLevel;

        mysqli_query($this->conn, "
            UPDATE experience
            SET level = $newLevel
            WHERE user_id = $userId
        ");

    }
}