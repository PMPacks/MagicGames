<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\Armor;
use pocketmine\item\ItemIds;
use pocketmine\item\ArmorTypeInfo;
use pocketmine\item\ItemIdentifier;
use pocketmine\inventory\ArmorInventory;

class ElytraItem extends Armor
{

    public function __construct()
    {
        parent::__construct(new ItemIdentifier(ItemIds::ELYTRA, 0), "Elytra", new ArmorTypeInfo(1, 432, ArmorInventory::SLOT_CHEST));
    }

    public function getMaxStackSize(): int
    {
        return 1;
    }
}
