<?php

declare(strict_types=1);

namespace BhawaniSingh\HCMinion\entities\types;

use pocketmine\block\Air;
use pocketmine\item\Item;
use pocketmine\block\Crops;
use pocketmine\block\Farmland;
use pocketmine\item\ItemFactory;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\StringToItemParser;
use BhawaniSingh\HCMinion\entities\MinionEntity;

class FarmingMinion extends MinionEntity
{
    public function getTarget(): void
    {
        $blocks = [];
        for ($x = -$this->getMinionRange(); $x <= $this->getMinionRange(); ++$x) {
            for ($z = -$this->getMinionRange(); $z <= $this->getMinionRange(); ++$z) {
                if ($x === 0 && $z === 0) {
                    continue;
                }

                $block = $this->getWorld()->getBlock($this->getPosition()->add($x, 0, $z));
                if ($block instanceof Air || $block->getId() === $this->getMinionInformation()->getType()->getTargetId()) {
                    $blocks[] = $block;
                }
            }
        }
        var_dump($blocks);
        if (count($blocks) > 0) {
            $this->target = $blocks[array_rand($blocks)];
            return;
        }
    }

    public function startWorking(): bool
    {
        if (!parent::startWorking()) {
            $farmland = $this->getWorld()->getBlock($this->target->getPosition()->add(0, -1, 0));
            if (!$farmland instanceof Farmland) {
                $this->getWorld()->setBlock($farmland->getPosition(), VanillaBlocks::FARMLAND(), false);
                return false;
            }
            /** @var Crops */
            $block = $this->getMinionInformation()->getType()->toBlock();
            $block->setAge(7);
    
            $this->getWorld()->setBlock($this->target->getPosition(), $block, false);
            return false;
        }
        return true;
    }

    public function getTool(string $tool, bool $isNetheriteTool): ?Item
    {
        return $isNetheriteTool ? ItemFactory::getInstance()->get(747) : StringToItemParser::getInstance()->parse($tool . ' Hoe');
    }

    public function broadcastPlaceBreak(): bool
    {
        return false;
    }

    public function isWorkFast(): bool
    {
        return true;
    }
}
