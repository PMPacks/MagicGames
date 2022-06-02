<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemIdentifier;

class EnchantedBookItem extends Item
{

    public function __construct()
    {
        parent::__construct(new ItemIdentifier(ItemIds::ENCHANTED_BOOK, 0), "Enchanted Book");
    }

    public function getMaxStackSize(): int
    {
        return 1;
    }
}
