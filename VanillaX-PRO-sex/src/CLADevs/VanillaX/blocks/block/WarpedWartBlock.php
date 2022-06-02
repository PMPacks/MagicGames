<?php

namespace CLADevs\VanillaX\blocks\block;

use pocketmine\block\Opaque;
use pocketmine\block\BlockToolType;
use pocketmine\block\BlockBreakInfo;
use CLADevs\VanillaX\blocks\BlockIds;
use pocketmine\block\BlockIdentifier;
use CLADevs\VanillaX\items\LegacyItemIds;

class WarpedWartBlock extends Opaque
{

    //TODO placable in Composter
    public function __construct()
    {
        parent::__construct(new BlockIdentifier(BlockIds::WARPED_WART_BLOCK, 0, LegacyItemIds::WARPED_WART_BLOCK), "Warped Wart Block", new BlockBreakInfo(1, BlockToolType::HOE));
    }
}
