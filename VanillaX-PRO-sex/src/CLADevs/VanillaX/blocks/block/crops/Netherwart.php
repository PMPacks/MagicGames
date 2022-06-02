<?php

namespace CLADevs\VanillaX\blocks\block\crops;

use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemFactory;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\NetherWartPlant;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\data\bedrock\EnchantmentIdMap;

class Netherwart extends NetherWartPlant
{

    public function __construct()
    {
        parent::__construct(new BlockIdentifier(BlockLegacyIds::NETHER_WART_PLANT, 0, ItemIds::NETHER_WART), "Nether Wart", BlockBreakInfo::instant());
    }

    /**
     * @param Item $item
     * @return Item[]
     */
    public function getDropsForCompatibleTool(Item $item): array
    {
        return [ItemFactory::getInstance()->get($this->asItem()->getId(), 0, min(7, ($this->getMeta() === 3 ? mt_rand(2, 4) + $item->getEnchantmentLevel(EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::FORTUNE)) : 1)))];
    }
}
