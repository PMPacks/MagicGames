<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\Item;
use pocketmine\entity\Human;
use pocketmine\entity\Living;
use pocketmine\entity\Consumable;
use pocketmine\item\VanillaItems;
use pocketmine\item\ItemIdentifier;
use CLADevs\VanillaX\items\LegacyItemIds;
use pocketmine\entity\effect\VanillaEffects;

class HoneyBottleItem extends Item implements Consumable
{

    public function __construct()
    {
        parent::__construct(new ItemIdentifier(LegacyItemIds::HONEY_BOTTLE, 0), "Honey Bottle");
    }

    public function onConsume(Living $consumer): void
    {
        $consumer->getEffects()->remove(VanillaEffects::POISON());

        if ($consumer instanceof Human) {
            $consumer->getHungerManager()->addFood(3);
            $consumer->getHungerManager()->addSaturation(1.2);
        }
    }

    public function getMaxStackSize(): int
    {
        return 16;
    }

    public function getResidue(): Item
    {
        return VanillaItems::GLASS_BOTTLE();
    }

    public function getAdditionalEffects(): array
    {
        return [];
    }
}
