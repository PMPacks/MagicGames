<?php

namespace CLADevs\VanillaX\blocks\block\bee;

use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\math\Facing;
use pocketmine\block\Opaque;
use pocketmine\math\Vector3;
use pocketmine\item\ToolTier;
use pocketmine\player\Player;
use pocketmine\block\BlockToolType;
use pocketmine\block\BlockBreakInfo;
use CLADevs\VanillaX\blocks\BlockIds;
use pocketmine\block\BlockIdentifier;
use pocketmine\world\BlockTransaction;
use CLADevs\VanillaX\items\LegacyItemIds;
use CLADevs\VanillaX\blocks\utils\AnyFacingTrait;
use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;

class BeehiveBlock extends Opaque implements NonAutomaticCallItemTrait
{
    use AnyFacingTrait;

    public function __construct(int $meta = 0)
    {
        parent::__construct(new BlockIdentifier(BlockIds::BEEHIVE, $meta, LegacyItemIds::BEEHIVE), "Beehive", new BlockBreakInfo(0.6, BlockToolType::AXE, ToolTier::STONE()->getHarvestLevel(), 0.6));
    }

    public function place(BlockTransaction $tx, Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, ?Player $player = null): bool
    {
        $this->facing = match ($player->getHorizontalFacing()) {
            Facing::NORTH => Facing::DOWN,
            Facing::EAST => Facing::UP,
            Facing::SOUTH => Facing::NORTH,
            Facing::WEST => Facing::SOUTH,
            default => $face
        };
        return parent::place($tx, $item, $blockReplace, $blockClicked, $face, $clickVector, $player);
    }
}
