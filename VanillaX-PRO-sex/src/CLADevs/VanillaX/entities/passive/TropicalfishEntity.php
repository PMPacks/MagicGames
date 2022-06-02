<?php

namespace CLADevs\VanillaX\entities\passive;

use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemFactory;
use pocketmine\nbt\tag\CompoundTag;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use CLADevs\VanillaX\entities\utils\EntityClassification;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class TropicalfishEntity extends VanillaEntity
{

    const NETWORK_ID = EntityIds::TROPICALFISH;

    public $width = 0.4;
    public $height = 0.4;

    protected function initEntity(CompoundTag $nbt): void
    {
        parent::initEntity($nbt);
        $this->setMaxHealth(6);
    }

    public function getName(): string
    {
        return "Tropical Fish";
    }

    /**
     * @return Item[]
     */
    public function getDrops(): array
    {
        $bone = ItemFactory::getInstance()->get(ItemIds::BONE, 0, 1);
        ItemHelper::applyLootingEnchant($this, $bone);
        return [ItemFactory::getInstance()->get(ItemIds::CLOWNFISH, 0, 1), $bone];
    }

    public function getXpDropAmount(): int
    {
        return $this->getLastHitByPlayer() ? mt_rand(1, 3) : 0;
    }

    public function getClassification(): int
    {
        return EntityClassification::AQUATIC;
    }
}
