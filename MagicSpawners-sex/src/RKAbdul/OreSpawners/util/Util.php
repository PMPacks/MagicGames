<?php

declare(strict_types=1);

namespace RKAbdul\OreSpawners\util;

use pocketmine\block\Block;
use RKAbdul\OreSpawners\Main;
use pocketmine\block\VanillaBlocks;
use DenielWorld\EzTiles\tile\SimpleTile;

class Util
{
    private Main $plugin;
    private array $cfg;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
        $this->cfg = $this->plugin->getConfig()->getAll();
    }

    public function checkBlock(Block $bBelow): Block|bool
    {
        $bbId = $bBelow->getId();
        $coalId = (int) $this->cfg["ore-generator-blocks"]["coal"];
        $ironId = (int) $this->cfg["ore-generator-blocks"]["iron"];
        $goldId = (int) $this->cfg["ore-generator-blocks"]["gold"];
        $diamondId = (int) $this->cfg["ore-generator-blocks"]["diamond"];
        $emeraldId = (int) $this->cfg["ore-generator-blocks"]["emerald"];
        $lapisId = (int) $this->cfg["ore-generator-blocks"]["lapis"];
        $redstoneId = (int) $this->cfg["ore-generator-blocks"]["redstone"];

        $ore = match ($bbId) {
            $coalId => VanillaBlocks::COAL_ORE(),
            $ironId => VanillaBlocks::IRON_ORE(),
            $goldId => VanillaBlocks::GOLD_ORE(),
            $diamondId => VanillaBlocks::DIAMOND_ORE(),
            $emeraldId => VanillaBlocks::EMERALD_ORE(),
            $lapisId => VanillaBlocks::LAPIS_LAZULI_ORE(),
            $redstoneId => VanillaBlocks::REDSTONE_ORE(),
            default => false
        };

        return $ore;
    }

    public function checkSpawner(Block $bBelow): string|bool
    {
        $bbId = $bBelow->getId();
        $coalId = (int) $this->cfg["ore-generator-blocks"]["coal"];
        $ironId = (int) $this->cfg["ore-generator-blocks"]["iron"];
        $goldId = (int) $this->cfg["ore-generator-blocks"]["gold"];
        $diamondId = (int) $this->cfg["ore-generator-blocks"]["diamond"];
        $emeraldId = (int) $this->cfg["ore-generator-blocks"]["emerald"];
        $lapisId = (int) $this->cfg["ore-generator-blocks"]["lapis"];
        $redstoneId = (int) $this->cfg["ore-generator-blocks"]["redstone"];

        $ore = match ($bbId) {
            $coalId => "coal",
            $ironId => "iron",
            $goldId => "gold",
            $diamondId => "diamond",
            $emeraldId => "emerald",
            $lapisId => "lapis",
            $redstoneId => "redstone",
            default => false
        };

        return $ore;
    }

    public function getDelay(Block $block): int
    {
        $tile = $block->getPosition()->getWorld()->getTile($block->getPosition());

        if ($tile instanceof SimpleTile) {
            $stacked = $tile->getData("stacked")->getValue();
            $base = (int) $this->cfg["base-delay"];
            return ($base / $stacked) * 20;
        }
        return 40;
    }
}
