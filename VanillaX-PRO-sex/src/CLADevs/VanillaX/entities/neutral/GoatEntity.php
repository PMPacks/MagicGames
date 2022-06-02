<?php

namespace CLADevs\VanillaX\entities\neutral;

use pocketmine\nbt\tag\CompoundTag;
use CLADevs\VanillaX\entities\VanillaEntity;

class GoatEntity extends VanillaEntity
{

    const NETWORK_ID = "minecraft:goat";

    public float $width = 0.9;
    public float $height = 1.3;

    protected function initEntity(CompoundTag $nbt): void
    {
        parent::initEntity($nbt);
        $this->setMaxHealth(10);
    }

    public function getName(): string
    {
        return "Goat";
    }

    public function getXpDropAmount(): int
    {
        return $this->getLastHitByPlayer() ? mt_rand(1, 3) : 0;
    }
}
