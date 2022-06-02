<?php

namespace SchdowNVIDIA\ECTUI;

use pocketmine\block\Block;
use pocketmine\plugin\PluginBase;
use pocketmine\block\BlockLegacyIds;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\data\bedrock\EnchantmentIdMap;

class Main extends PluginBase
{
    public function onEnable(): void
    {
        $this->initEnchantments();

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
    }

    public function initEnchantments(): void
    {
        EnchantmentIdMap::getInstance()->register(EnchantmentIds::FORTUNE, new Enchantment("fortune", Rarity::UNCOMMON, ItemFlags::DIG, ItemFlags::NONE, 3));
        EnchantmentIdMap::getInstance()->register(EnchantmentIds::LOOTING, new Enchantment("looting", Rarity::UNCOMMON, ItemFlags::SWORD, ItemFlags::NONE, 3));
    }

    public function getBookshelves(Block $ectable): int
    {
        $count = 0;
        $level = $ectable->getPosition()->getWorld();

        $bx = (int) $ectable->getPosition()->getX();
        $by = (int) $ectable->getPosition()->getY();
        $bz = (int) $ectable->getPosition()->getZ();
        
        // Right
        for ($i = 0; $i <= 2; $i++) {
            for ($ii = 0; $ii <= 2; $ii++) {
                if ($i === 0) {
                    if ($level->getBlockAt($bx, $by + $ii, $bz + 2)->getId() === BlockLegacyIds::BOOKSHELF) {
                        $count++;
                    }
                } else {
                    if ($level->getBlockAt($bx + $i, $by + $ii, $bz + 2)->getId() === BlockLegacyIds::BOOKSHELF) {
                        $count++;
                    }
                    if ($level->getBlockAt($bx - $i, $by + $ii, $bz + 2)->getId() === BlockLegacyIds::BOOKSHELF) {
                        $count++;
                    }
                }
            }
        }
        // Left
        for ($i = 0; $i <= 2; $i++) {
            for ($ii = 0; $ii <= 2; $ii++) {
                if ($i === 0) {
                    if ($level->getBlockAt($bx, $by + $ii, $bz - 2)->getId() === BlockLegacyIds::BOOKSHELF) {
                        $count++;
                    }
                } else {
                    if ($level->getBlockAt($bx + $i, $by + $ii, $bz - 2)->getId() === BlockLegacyIds::BOOKSHELF) {
                        $count++;
                    }
                    if ($level->getBlockAt($bx - $i, $by + $ii, $bz - 2)->getId() === BlockLegacyIds::BOOKSHELF) {
                        $count++;
                    }
                }
            }
        }
        // Top
        for ($i = 0; $i <= 1; $i++) {
            for ($ii = 0; $ii <= 2; $ii++) {
                if ($i === 0) {
                    if ($level->getBlockAt($bx + 2, $by + $ii, $bz)->getId() === BlockLegacyIds::BOOKSHELF) {
                        $count++;
                    }
                } else {
                    if ($level->getBlockAt($bx + 2, $by + $ii, $bz + $i)->getId() === BlockLegacyIds::BOOKSHELF) {
                        $count++;
                    }
                    if ($level->getBlockAt($bx + 2, $by + $ii, $bz - $i)->getId() === BlockLegacyIds::BOOKSHELF) {
                        $count++;
                    }
                }
            }
        }
        // Bottom
        for ($i = 0; $i <= 1; $i++) {
            for ($ii = 0; $ii <= 2; $ii++) {
                if ($i === 0) {
                    if ($level->getBlockAt($bx - 2, $by + $ii, $bz)->getId() === BlockLegacyIds::BOOKSHELF) {
                        $count++;
                    }
                } else {
                    if ($level->getBlockAt($bx - 2, $by + $ii, $bz + $i)->getId() === BlockLegacyIds::BOOKSHELF) {
                        $count++;
                    }
                    if ($level->getBlockAt($bx - 2, $by + $ii, $bz - $i)->getId() === BlockLegacyIds::BOOKSHELF) {
                        $count++;
                    }
                }
            }
        }
        return $count;
    }
}
