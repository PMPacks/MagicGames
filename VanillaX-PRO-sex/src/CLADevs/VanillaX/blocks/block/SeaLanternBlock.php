<?php

namespace CLADevs\VanillaX\blocks\block;

use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\block\SeaLantern;
use pocketmine\item\ItemFactory;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockIdentifier;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\data\bedrock\EnchantmentIdMap;

class SeaLanternBlock extends SeaLantern
{

    public function __construct()
    {
        parent::__construct(new BlockIdentifier(BlockLegacyIds::SEALANTERN, 0), "Sea Lantern", new BlockBreakInfo(0.3));
    }

    /**
     * @param Item $item
     * @return Item[]
     */
    public function getDropsForCompatibleTool(Item $item): array
    {
        return [ItemFactory::getInstance()->get(ItemIds::PRISMARINE_CRYSTALS, 0, min(5, mt_rand(2, 3) + $item->getEnchantmentLevel(EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::FORTUNE))))];
    }
}
