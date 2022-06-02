<?php

namespace CLADevs\VanillaX\blocks\block\nylium;

use pocketmine\block\Block;
use pocketmine\math\Facing;
use pocketmine\block\VanillaBlocks;
use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;

class Nylium extends Block implements NonAutomaticCallItemTrait
{

    public function onRandomTick(): void
    {
        if ($this->getSide(Facing::UP)->isSolid()) {
            $this->position->getWorld()->setBlock($this->position, VanillaBlocks::NETHERRACK());
        }
    }

    public function ticksRandomly(): bool
    {
        return true;
    }
}
