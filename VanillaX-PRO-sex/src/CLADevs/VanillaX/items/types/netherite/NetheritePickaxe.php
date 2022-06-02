<?php

namespace CLADevs\VanillaX\items\types\netherite;

use pocketmine\item\Pickaxe;
use pocketmine\item\ToolTier;
use pocketmine\item\ItemIdentifier;
use CLADevs\VanillaX\items\LegacyItemIds;

class NetheritePickaxe extends Pickaxe
{

    public function __construct()
    {
        parent::__construct(new ItemIdentifier(LegacyItemIds::NETHERITE_PICKAXE, 0), "Netherite Pickaxe", ToolTier::DIAMOND());
    }

    public function getAttackPoints(): int
    {
        return 7; //Netherite Pickaxe Damage
    }

    public function getMaxDurability(): int
    {
        return 2032;
    }

    protected function getBaseMiningEfficiency(): float
    {
        return 10;
    }
}
