<?php

declare(strict_types=1);

namespace BhawaniSingh\HCMinion\entities\types;

use pocketmine\block\Air;
use pocketmine\item\Item;
use pocketmine\utils\Random;
use pocketmine\item\ItemFactory;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\BlockLegacyIds;
use pocketmine\item\StringToItemParser;
use BhawaniSingh\HCMinion\entities\MinionEntity;
use pocketmine\world\particle\BlockBreakParticle;

class LumberjackMinion extends MinionEntity
{
    public function updateTarget(): void
    {
        for ($x = -2; $x <= 2; ++$x) {
            for ($z = -2; $z <= 2; ++$z) {
                if ($x === 0 && $z === 0) {
                    continue;
                }
                if (($x % 2 === 0 && $z % 2 === 0)) {
                    $block = $this->getWorld()->getBlock($this->getPosition()->add($x, 0, $z));
                    if ($block->getId() === BlockLegacyIds::SAPLING) {
                        if (mt_rand(0, 1) === 0) {
                            $this->getMinionInformation()->getType()->toTree()->placeObject($this->getWorld(), (int)$block->getPosition()->getX(), (int)$block->getPosition()->getY(), (int)$block->getPosition()->getZ(), new Random());
                        }
                    }
                }
            }
        }
    }

    public function getTarget(): void
    {
        $blocks = [];
        for ($x = -2; $x <= 2; ++$x) {
            for ($z = -2; $z <= 2; ++$z) {
                if ($x === 0 && $z === 0) {
                    continue;
                }
                $dirt = $this->getWorld()->getBlock($this->getPosition()->add($x, -1, $z));
                $block = $this->getWorld()->getBlock($this->getPosition()->add($x, 0, $z));
                if (($x % 2 === 0 && $z % 2 === 0) || ($this->getMinionInformation()->getUpgrade()->isSuperExpander() && (abs($x) === 1 && abs($z) === 1))) {
                    if (in_array($dirt->getId(), [VanillaBlocks::GRASS()->getId(), VanillaBlocks::DIRT()->getId(), VanillaBlocks::FARMLAND()->getId()], true) && ($block->getId() === VanillaBlocks::AIR()->getId() || ($block->getId() === $this->getMinionInformation()->getType()->getTargetId()))) {
                        $blocks[] = $block;
                    }
                }
            }
        }
        if (count($blocks) > 0) {
            $this->target = $blocks[array_rand($blocks)];
        }
    }

    public function startWorking(): bool
    {
        if (!$this->target instanceof Air) {
            for ($y = 0; $y < 4; ++$y) {
                $block = $this->getWorld()->getBlock($this->target->getPosition()->add(0, $y, 0));
                if ($block->getId() !== $this->getMinionInformation()->getType()->getTargetId()) {
                    $this->stopWorking();
                    break;
                }
                $this->getWorld()->addParticle($block->getPosition()->add(0.5, 0.5, 0.5), new BlockBreakParticle($block));
                $this->getWorld()->setBlock($block->getPosition(), VanillaBlocks::AIR(), false);
                $drop = ItemFactory::getInstance()->get($block->getId(), $block->getMeta());
                for ($i = 1; $i <= $drop->getCount(); ++$i) {
                    $thing = ItemFactory::getInstance()->get($drop->getId(), $drop->getMeta());
                    if ($this->getMinionInventory()->canAddItem($thing)) {
                        $this->getMinionInventory()->addItem($thing);
                        $this->getMinionInformation()->incrementResourcesCollected();
                    }
                }
            }
            return true;
        }
        $this->getWorld()->setBlock($this->target->getPosition(), $this->getMinionInformation()->getType()->toTree()->sapling, false);
        return false;
    }

    public function getTool(string $tool, bool $isNetheriteTool): ?Item
    {
        return $isNetheriteTool ? ItemFactory::getInstance()->get(746) : StringToItemParser::getInstance()->parse($tool . ' Axe');
    }

    protected function canUseAutoSmelter(): bool
    {
        return false;
    }

    public function canUseExpander(): bool
    {
        return false;
    }
}
