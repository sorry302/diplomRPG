<?php

class ExperienceService
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function addExp(int $userId, int $amount): void
    {
        // Добавляем опыт
        $stmt = $this->db->prepare("
            UPDATE experience
            SET exp = exp + ?
            WHERE user_id = ?
        ");
        $stmt->execute([$amount, $userId]);

        // Проверяем ап уровня
        $this->checkLevelUp($userId);
    }

    private function checkLevelUp(int $userId): void
    {
        // Текущий опыт и уровень
        $stmt = $this->db->prepare("
            SELECT exp, level
            FROM experience
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        $expData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$expData) {
            return;
        }

        $currentExp = (int)$expData['exp'];
        $currentLevel = (int)$expData['level'];

        // Сколько нужно XP для следующего уровня
        $stmt = $this->db->prepare("
            SELECT required_exp
            FROM levels
            WHERE level = ?
        ");
        $stmt->execute([$currentLevel + 1]);
        $nextLevel = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$nextLevel) {
            return; // максимальный уровень
        }

        if ($currentExp >= $nextLevel['required_exp']) {
            $this->levelUp($userId, $currentLevel + 1);
        }
    }

    private function levelUp(int $userId, int $newLevel): void
    {
        $stmt = $this->db->prepare("
            UPDATE experience
            SET level = ?
            WHERE user_id = ?
        ");
        $stmt->execute([$newLevel, $userId]);

        // 💡 Здесь потом:
        // + восстановление энергии
        // + бонус к статам
        // + уведомление игроку
    }
}
