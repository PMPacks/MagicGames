<?php

namespace CLADevs\VanillaX\items\types\netherite;

use pocketmine\item\Armor;
use pocketmine\item\ArmorTypeInfo;
use pocketmine\item\ItemIdentifier;
use pocketmine\inventory\ArmorInventory;
use CLADevs\VanillaX\items\LegacyItemIds;

class NetheriteChestplate extends Armor
{

    public function __construct()
    {
        parent::__construct(new ItemIdentifier(LegacyItemIds::NETHERITE_CHESTPLATE, 0), "Netherite Chestplate", new ArmorTypeInfo(5, 593, ArmorInventory::SLOT_CHEST));
    }
}
