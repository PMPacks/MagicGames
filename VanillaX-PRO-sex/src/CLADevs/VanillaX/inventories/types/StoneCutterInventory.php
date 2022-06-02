<?php

namespace CLADevs\VanillaX\inventories\types;

use pocketmine\world\Position;
use pocketmine\block\BlockLegacyIds;
use CLADevs\VanillaX\inventories\FakeBlockInventory;
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;

class StoneCutterInventory extends FakeBlockInventory
{

    public function __construct(Position $holder)
    {
        parent::__construct($holder, 1, BlockLegacyIds::AIR, WindowTypes::STONECUTTER);
    }
}
