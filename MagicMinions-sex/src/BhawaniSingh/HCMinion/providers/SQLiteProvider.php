<?php

namespace BhawaniSingh\HCMinion\providers;

use SQLite3;
use SQLite3Stmt;
use SQLite3Result;
use RuntimeException;
use BhawaniSingh\HCMinion\BetterMinion;

class SQLiteProvider extends Provider
{
	private SQLite3 $database;

	public function __construct()
	{
		$this->database = new SQLite3(BetterMinion::getInstance()->getDataFolder() . "minion.db");
		if (!$this->database->exec("CREATE TABLE IF NOT EXISTS minion (playerName VARCHAR(40), minionCount INTEGER)")) {
			throw new RuntimeException("Failed to create table quest. Please consult a doctor.");
		}
	}

	public function createMinionData(string $playerName): void
	{
		$stmt = $this->database->prepare("INSERT INTO minion (playerName, minionCount) VALUES (:playerName, :minionCount)");
		if (!$stmt instanceof SQLite3Stmt) {
			return;
		}

		$stmt->bindValue(":playerName", $playerName);
		$stmt->bindValue(":minionCount", 0);
		$stmt->execute();
	}

	public function getMinionDataFromPlayer(string $playerName): array
	{
		$result = $this->database->query("SELECT * FROM minion WHERE playerName='{$playerName}'");
		if (!$result instanceof SQLite3Result) {
			return [];
		}
		$fetch = $result->fetchArray(SQLITE3_ASSOC);
		if (!is_array($fetch)) {
			return [];
		}

		return $fetch;
	}

	public function updateMinionData(string $playerName, int $newMinionCount): void
	{
		$stmt = $this->database->prepare("UPDATE minion SET minionCount=:minionCount WHERE playerName='{$playerName}'");
		if (!$stmt instanceof SQLite3Stmt) {
			return;
		}

		$stmt->bindValue(":playerName", $playerName);
		$stmt->bindValue(":minionCount", $newMinionCount);
		$stmt->execute();
	}

	public function hasMinionData(string $playerName): bool
	{
		$stmt = $this->database->prepare("SELECT * FROM minion WHERE playerName='{$playerName}'");
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
}
