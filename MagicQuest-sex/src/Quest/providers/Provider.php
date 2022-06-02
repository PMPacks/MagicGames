<?php

namespace Quest\providers;

abstract class Provider
{
    abstract public function addQuest(string $playerName, string $quest): void;

    abstract public function removeQuest(string $playerName): void;

    abstract public function hasQuest(string $playerName): bool;
}
