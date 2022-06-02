<?php

namespace CLADevs\VanillaX\items\types\netherite;

use pocketmine\item\Armor;
use pocketmine\item\ArmorTypeInfo;
use pocketmine\item\ItemIdentifier;
use pocketmine\inventory\ArmorInventory;
use CLADevs\VanillaX\items\LegacyItemIds;

class NetheriteHelmet extends Armor
{

    public function __construct()
    {
        parent::__construct(new ItemIdentifier(LegacyItemIds::NETHERITE_HELMET, 0), "Netherite Helmet", new ArmorTypeInfo(3, 408, ArmorInventory::SLOT_HEAD));
    }
}
