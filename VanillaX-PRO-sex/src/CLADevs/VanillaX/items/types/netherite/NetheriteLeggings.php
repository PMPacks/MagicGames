<?php

namespace CLADevs\VanillaX\items\types\netherite;

use pocketmine\item\Armor;
use pocketmine\item\ArmorTypeInfo;
use pocketmine\item\ItemIdentifier;
use pocketmine\inventory\ArmorInventory;
use CLADevs\VanillaX\items\LegacyItemIds;

class NetheriteLeggings extends Armor
{

    public function __construct()
    {
        parent::__construct(new ItemIdentifier(LegacyItemIds::NETHERITE_LEGGINGS, 0), "Netherite Leggings", new ArmorTypeInfo(6, 556, ArmorInventory::SLOT_LEGS));
    }
}
