<?php

class StatsInitializer //Мы создаём класс, потому что:
                        // есть состояние (PDO)
                        // есть ответственность
                        // есть методы
                        // ❌ не function
                        // ❌ не static helper
{
    private PDO $db; //Здесь мы храним подключение к БД. Почему private: никто снаружи не должен его менять Почему тип PDO:безопасность IDE помогает 

    public function __construct(PDO $db) //Конструктор получает PDO снаружи. Класс сам не создаёт БД, ему её передают. называется"Dependency Injection" 
    {
        $this->db = $db;
    }

    public function init(int $userId, array $profile): void //public function init(...) нельзя инициализировать по частям
    {
        if ($this->hasStats($userId)) { //защита от: багов, читинга, повторного вызова
            throw new RuntimeException('Stats already initialized'); // сигнашка
        }

        $stats = $this->getAllStats();//Мы НЕ знаем, какие статы есть в игре. Мы их читаем из БД.

        foreach ($stats as $stat) { // Мы создаём статы динамически
            $value = $this->calculateStat($stat['code'], $profile); //логика баланса
            $this->insertUserStat($userId, $stat['id'], $value);//дает читабельность, легко модифицировать
        }
    }

    private function hasStats(int $userId): bool //проверка на статы
    {
        $stmt = $this->db->prepare(
            "SELECT 1 FROM user_stats WHERE user_id = ? LIMIT 1"
        );
        $stmt->execute([$userId]);
        return (bool) $stmt->fetchColumn();
    }

    private function getAllStats(): array //select
    {
        $stmt = $this->db->query("SELECT * FROM stats");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function calculateStat(string $code, array $profile): int
    {
        return match ($code) {
            'health'   => $this->calculateHealth($profile),
            'energy'   => 100,
            'fat'      => $this->calculateFat($profile),
            'intellect'=> 50,
            default    => 0
        };
    }

    private function insertUserStat(int $userId, int $statId, int $value): void
    {
        $stmt = $this->db->prepare(
            "INSERT INTO user_stats (user_id, stat_id, value)
             VALUES (?, ?, ?)"
        );
        $stmt->execute([$userId, $statId, $value]);
    }

    private function calculateHealth(array $profile): int//формула, можно усложнять, легко читать
    {
        return max(50, 120 - (int)($profile['age'] * 0.5));
    }

    private function calculateFat(array $profile): int //формула bmi
    {
        $heightM = $profile['height'] / 100;
        $bmi = $profile['weight'] / ($heightM * $heightM);
        return (int) round($bmi * 2);
    }
}
