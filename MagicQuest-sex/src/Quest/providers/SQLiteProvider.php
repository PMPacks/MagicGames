<?php

namespace Quest\providers;

use SQLite3;
use Quest\Quest;
use SQLite3Stmt;
use SQLite3Result;
use RuntimeException;

class SQLiteProvider extends Provider
{
    private SQLite3 $database;

    public function __construct()
    {
        $this->database = new SQLite3(Quest::getInstance()->getDataFolder() . "quest.db");
        if (!$this->database->exec("CREATE TABLE IF NOT EXISTS quest (playerName VARCHAR(40), quest VARCHAR(25), progress INTEGER)")) {
            throw new RuntimeException("Failed to create table quest. Please consult a doctor.");
        }
    }

    public function addQuest(string $playerName, string $quest): void
    {
        $stmt = $this->database->prepare("INSERT INTO quest (playerName, quest, progress) VALUES (:playerName, :quest, :progress)");
        if (!$stmt instanceof SQLite3Stmt) {
            return;
        }
        
        $stmt->bindValue(":playerName", $playerName);
        $stmt->bindValue(":quest", $quest);
        $stmt->bindValue(":progress", 0);
        $stmt->execute();
    }

    public function removeQuest(string $playerName): void
    {
        $stmt = $this->database->prepare("DELETE FROM quest WHERE playerName='{$playerName}'");
        if (!$stmt instanceof SQLite3Stmt) {
            return;
        }

        $stmt->bindValue(":playerName", $playerName);
        $stmt->execute();
    }

    public function hasQuest(string $playerName): bool
    {
        $stmt = $this->database->prepare("SELECT * FROM quest WHERE playerName='{$playerName}'");
        if (!$stmt instanceof SQLite3Stmt) {
            return false;
        }
        $stmt->bindValue(":playerName", $playerName);
        
        $execute = $stmt->execute();
        if (!$execute instanceof SQLite3Result) {
            return false;
        }
        $array = $execute->fetchArray(SQLITE3_ASSOC);
        if (!is_array($array)) {
            return false;
        }

        return count($array) > 0;
    }

    public function getQuestFromPlayer(string $playerName): array
    {
        $result = $this->database->query("SELECT * FROM quest WHERE playerName='{$playerName}'");
        if (!$result instanceof SQLite3Result) {
            return [];
        }
        $fetch = $result->fetchArray(SQLITE3_ASSOC);
        if (!is_array($fetch)) {
            return [];
        }

        return $fetch;
    }

    public function updateQuestFromPlayer(string $playerName, int $progress): void
    {
        $stmt = $this->database->prepare("UPDATE quest SET progress=:progress WHERE playerName='{$playerName}'");
        if (!$stmt instanceof SQLite3Stmt) {
            return;
        }
        
        $stmt->bindValue(":playerName", $playerName);
        $stmt->bindValue(":progress", $progress);
        $stmt->execute();
    }
}
