<?php

namespace BhawaniSingh\HCMinion\providers;

abstract class Provider
{
	abstract public function createMinionData(string $playerName): void;

	abstract public function updateMinionData(string $playerName, int $newMinionCount): void;

	abstract public function hasMinionData(string $playerName): bool;

	abstract public function getMinionDataFromPlayer(string $playerName): array;
}
