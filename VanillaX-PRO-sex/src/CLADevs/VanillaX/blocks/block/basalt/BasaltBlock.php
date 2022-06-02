<?php

namespace CLADevs\VanillaX\blocks\block\basalt;

use pocketmine\block\Opaque;
use pocketmine\block\BlockToolType;
use pocketmine\block\BlockBreakInfo;
use CLADevs\VanillaX\blocks\BlockIds;
use pocketmine\block\BlockIdentifier;
use CLADevs\VanillaX\items\LegacyItemIds;
use CLADevs\VanillaX\blocks\utils\BlockFacingOppositeTrait;

class BasaltBlock extends Opaque
{
    use BlockFacingOppositeTrait;

    public function __construct()
    {
        parent::__construct(new BlockIdentifier(BlockIds::BASALT, 0, LegacyItemIds::BASALT), "Basalt", new BlockBreakInfo(1.25, BlockToolType::PICKAXE, 0, 4.2));
    }
}
