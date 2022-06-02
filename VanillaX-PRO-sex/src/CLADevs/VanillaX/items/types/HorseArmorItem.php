<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemIdentifier;
use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;

class HorseArmorItem extends Item implements NonAutomaticCallItemTrait
{

    public function __construct(int $id, string $name = "Unknown")
    {
        switch ($id) {
            case ItemIds::LEATHER_HORSE_ARMOR:
                $name = "Leather Horse Armor";
                break;
            case ItemIds::IRON_HORSE_ARMOR:
                $name = "Iron Horse Armor";
                break;
            case ItemIds::GOLD_HORSE_ARMOR:
                $name = "Gold Horse Armor";
                break;
            case ItemIds::DIAMOND_HORSE_ARMOR:
                $name = "Diamond Horse Armor";
                break;
        }
        parent::__construct(new ItemIdentifier($id, 0), $name);
    }

    public function getMaxStackSize(): int
    {
        return 1;
    }
}
