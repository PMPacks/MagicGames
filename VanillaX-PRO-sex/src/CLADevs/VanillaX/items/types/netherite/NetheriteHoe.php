<?php

namespace CLADevs\VanillaX\items\types\netherite;

use pocketmine\item\Hoe;
use pocketmine\item\ToolTier;
use pocketmine\item\ItemIdentifier;
use CLADevs\VanillaX\items\LegacyItemIds;

class NetheriteHoe extends Hoe
{

    public function __construct()
    {
        parent::__construct(new ItemIdentifier(LegacyItemIds::NETHERITE_HOE, 0), "Netherite Hoe", ToolTier::DIAMOND());
    }

    public function getMaxDurability(): int
    {
        return 2032;
    }

    protected function getBaseMiningEfficiency(): float
    {
        return 12; //Netherite Hoe Speed
    }
}
