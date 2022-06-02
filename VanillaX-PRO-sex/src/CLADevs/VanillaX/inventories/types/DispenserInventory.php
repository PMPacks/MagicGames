<?php

namespace CLADevs\VanillaX\inventories\types;

use pocketmine\world\Position;
use pocketmine\block\BlockLegacyIds;
use CLADevs\VanillaX\inventories\FakeBlockInventory;
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;

class DispenserInventory extends FakeBlockInventory
{

    public function __construct(Position $holder)
    {
        parent::__construct($holder, 9, BlockLegacyIds::AIR, WindowTypes::DISPENSER);
    }
}
