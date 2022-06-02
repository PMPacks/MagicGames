<?php

declare(strict_types=1);

namespace BhawaniSingh\HCMinion\entities\objects;

use pocketmine\block\Block;
use pocketmine\world\World;
use pocketmine\block\Opaque;
use pocketmine\utils\Random;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\world\generator\object\Tree;

class MinionTree extends Tree
{
    /** @var Block */
    public $trunkBlock;
    /** @var Block */
    public $leafBlock;

    public Block $sapling;

    public function __construct(Block $log)
    {
        $this->trunkBlock = BlockFactory::getInstance()->get($log->getId(), $log->getMeta());
        $this->leafBlock = BlockFactory::getInstance()->get($log->getId() === BlockLegacyIds::WOOD ? BlockLegacyIds::LEAVES : BlockLegacyIds::LEAVES2, $log->getMeta());
        $this->sapling = BlockFactory::getInstance()->get(BlockLegacyIds::SAPLING, $log->getId() === BlockLegacyIds::WOOD ? $log->getMeta() : ($log->getMeta() + 4));
    
        parent::__construct($this->trunkBlock, $this->leafBlock, 4);
    }

    public function placeObject(World $world, int $x, int $y, int $z, Random $random): void
    {
        for ($yy = 0; $yy < 4; ++$yy) {
            $world->setBlockAt($x, $y + $yy, $z, $this->trunkBlock);
        }

        if (!$world->getBlockAt($x, $y + 4, $z) instanceof Opaque) {
            $world->setBlockAt($x, $y + 4, $z, $this->leafBlock);
        }
        $yy = $y + 3;
        for ($xx = $x - 1; $xx <= $x + 1; ++$xx) {
            for ($zz = $z - 1; $zz <= $z + 1; ++$zz) {
                if (!$world->getBlockAt($xx, $yy, $zz) instanceof Opaque) {
                    $world->setBlockAt($xx, $yy, $zz, $this->leafBlock);
                }
            }
        }
    }
}
