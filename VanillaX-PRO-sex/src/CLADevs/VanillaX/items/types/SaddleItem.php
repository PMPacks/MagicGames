<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemIdentifier;

class SaddleItem extends Item
{

    public function __construct()
    {
        parent::__construct(new ItemIdentifier(ItemIds::SADDLE, 0), "Saddle");
    }

    public function getMaxStackSize(): int
    {
        return 1;
    }
}
