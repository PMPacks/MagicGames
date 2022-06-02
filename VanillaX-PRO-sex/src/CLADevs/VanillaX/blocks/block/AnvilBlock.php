<?php

namespace CLADevs\VanillaX\blocks\block;

use pocketmine\item\Item;
use pocketmine\block\Anvil;
use pocketmine\math\Vector3;
use pocketmine\item\ToolTier;
use pocketmine\player\Player;
use pocketmine\block\BlockToolType;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockIdentifier;
use CLADevs\VanillaX\inventories\types\AnvilInventory;

class AnvilBlock extends Anvil
{

    public function __construct()
    {
        parent::__construct(new BlockIdentifier(BlockLegacyIds::ANVIL, 0), "Anvil", new BlockBreakInfo(5.0, BlockToolType::PICKAXE, ToolTier::WOOD()->getHarvestLevel(), 6000.0));
    }

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool
    {
        $player?->setCurrentWindow(new AnvilInventory($this->getPosition()));
        return true;
    }
}
