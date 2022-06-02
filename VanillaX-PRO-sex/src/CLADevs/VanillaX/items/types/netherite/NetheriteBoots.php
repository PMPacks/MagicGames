<?php

namespace CLADevs\VanillaX\items\types\netherite;

use pocketmine\item\Armor;
use pocketmine\item\ArmorTypeInfo;
use pocketmine\item\ItemIdentifier;
use pocketmine\inventory\ArmorInventory;
use CLADevs\VanillaX\items\LegacyItemIds;

class NetheriteBoots extends Armor
{

    public function __construct()
    {
        parent::__construct(new ItemIdentifier(LegacyItemIds::NETHERITE_BOOTS, 0), "Netherite Boots", new ArmorTypeInfo(3, 482, ArmorInventory::SLOT_FEET));
    }
}
