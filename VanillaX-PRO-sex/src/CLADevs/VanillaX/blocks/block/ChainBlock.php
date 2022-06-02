<?php

namespace CLADevs\VanillaX\blocks\block;

use pocketmine\block\Transparent;
use pocketmine\block\BlockToolType;
use pocketmine\block\BlockBreakInfo;
use CLADevs\VanillaX\blocks\BlockIds;
use pocketmine\block\BlockIdentifier;
use CLADevs\VanillaX\items\LegacyItemIds;
use CLADevs\VanillaX\blocks\utils\BlockFacingOppositeTrait;

class ChainBlock extends Transparent
{
    use BlockFacingOppositeTrait;

    public function __construct()
    {
        parent::__construct(new BlockIdentifier(BlockIds::CHAIN, 0, LegacyItemIds::CHAIN), "Chain", new BlockBreakInfo(5, BlockToolType::PICKAXE, 0, 6));
    }
}
