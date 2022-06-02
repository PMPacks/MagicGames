<?php

namespace CLADevs\VanillaX\inventories\types;

use pocketmine\world\Position;
use pocketmine\block\BlockLegacyIds;
use CLADevs\VanillaX\inventories\FakeBlockInventory;
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;

class HopperInventory extends FakeBlockInventory
{

    public function __construct(Position $holder)
    {
        parent::__construct($holder, 5, BlockLegacyIds::AIR, WindowTypes::HOPPER);
    }
}
