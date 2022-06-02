<?php

namespace Stats\player;

use pocketmine\Server;
use pocketmine\world\World;
use pocketmine\player\Player;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\network\mcpe\protocol\RespawnPacket;
use pocketmine\event\entity\EntityRegainHealthEvent;

class MagicPlayer extends Player
{
    public array $stats = [
        "MaxHealth" => 20,
        "Health" => 20,
        "Defense" => 0,
        "Damage" => 1,
    ];

    public function getHealth(): float
    {
        return $this->stats["Health"];
    }

    public function getMaxHealth(): int
    {
        return $this->stats["MaxHealth"];
    }

    public function getDefense(): int
    {
        return $this->stats["Defense"];
    }

    public function getDamage(): int
    {
        return $this->stats["Damage"];
    }

    public function setStats(string $stats, float $amount): void
    {
        switch ($stats) {
            case "MaxHealth":
                if ($amount <= 0) {
                    $this->setMaxHealth(1);
                }
                break;
            case "Health":
                if ($amount <= 0) {
                    $this->onDeath();
                    break;
                }
                if ($amount > $this->getMaxHealth()) {
                    $this->setHealth($this->getMaxHealth());
                    break;
                }
                if ($amount < $this->getHealth()) {
                    $this->setHealth($amount);
                    $this->setMaxHealth((int)$amount);
                }
                break;
            default:
                $this->stats[$stats] = $amount;
                break;
        }
    }

    public function setHealth(float $amount): void
    {
        switch (true) {
            case $amount <= 0:
                $this->stats["Health"] = 0;
                break;
            case $amount > $this->getMaxHealth():
                $this->stats["Health"] = $this->getMaxHealth();
                break;
            default:
                $this->stats["Health"] = $amount;
                break;
        }
        parent::setHealth($this->stats["Health"]);
    }

    public function heal(EntityRegainHealthEvent $source): void
    {
        $source->call();
        if ($source->isCancelled()) {
            return;
        }
        $this->setHealth($this->getHealth() + $source->getAmount());
    }

    public function setMaxHealth(int $amount): void
    {
        $this->stats["MaxHealth"] = $amount;
        parent::setMaxHealth($amount);
    }
}
