<?php

declare(strict_types=1);

namespace BhawaniSingh\HCMinion\entities\types;

use pocketmine\block\Air;
use BhawaniSingh\HCMinion\entities\MinionEntity;

class MiningMinion extends MinionEntity
{
    public function getTarget(): void
    {
        $blocks = [];
        for ($x = -$this->getMinionRange(); $x <= $this->getMinionRange(); ++$x) {
            for ($z = -$this->getMinionRange(); $z <= $this->getMinionRange(); ++$z) {
                if ($x === 0 && $z === 0) {
                    continue;
                }
                
                $block = $this->getWorld()->getBlock($this->getPosition()->add($x, -1, $z));
                if ($block instanceof Air || ($block->getId() === $this->getMinionInformation()->getType()->getTargetId() && $block->getMeta() === $this->getMinionInformation()->getType()->getTargetMeta())) {
                    $blocks[] = $block;
                }
            }
        }
        if (count($blocks) > 0) {
            $this->target = $blocks[array_rand($blocks)];
        }
    }
}
