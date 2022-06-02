<?php

declare(strict_types=1);

namespace RKAbdul\OreSpawners;

use pocketmine\block\Block;
use pocketmine\event\Listener;
use pocketmine\nbt\tag\StringTag;
use RKAbdul\OreSpawners\util\Util;
use pocketmine\scheduler\ClosureTask;
use pocketmine\world\sound\FizzSound;
use DenielWorld\EzTiles\data\TileInfo;
use pocketmine\utils\TextFormat as TF;
use DenielWorld\EzTiles\tile\SimpleTile;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\event\player\PlayerInteractEvent;

class EventListener implements Listener
{
    private Main $plugin;
    private Util $util;

    private array $cfg;

    public function __construct(Main $plugin, Util $util)
    {
        $this->plugin = $plugin;
        $this->util = $util;

        $this->cfg = $this->plugin->getConfig()->getAll();
    }

    public function onBlockUpdate(BlockUpdateEvent $event): void
    {
        $block = $event->getBlock();
        $pos = $block->getPosition();

        $bBelow = $pos->getWorld()->getBlock($pos->floor()->down(1));
        $blocks = [];

        foreach (array_values($this->plugin->getConfig()->get("ore-generator-blocks")) as $blockID) {
            array_push($blocks, $blockID);
        }

        if (in_array($bBelow->getId(), $blocks)) {
            $tile = $pos->getWorld()->getTile($pos);
            if (!$tile instanceof SimpleTile) {
                return;
            }

            $ore = $this->util->checkBlock($bBelow);
            $delay = $this->util->getDelay($bBelow);
            if (!$event->isCancelled() && $ore instanceof Block) {
                $event->cancel();
                if ($block->getId() == $ore->getId()) {
                    return;
                }

                $this->plugin->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($pos, $ore): void {
                    $pos->getWorld()->setBlock($pos->floor(), $ore, false);
                    if ($this->cfg["fizz-sound"] == true) {
                        $pos->getWorld()->addSound($pos, new FizzSound());
                    }
                }), $delay);
            }
        }
    }

    public function onBlockPlace(BlockPlaceEvent $event): void
    {
        $block = $event->getBlock();
        $pos = $block->getPosition();

        $item = $event->getItem();
        $blocks = [];
        foreach (array_values($this->plugin->getConfig()->get("ore-generator-blocks")) as $blockID) {
            array_push($blocks, $blockID);
        }

        if (in_array($block->getId(), $blocks)) {
            if ($item->getNamedTag()->getTag("orespawner") instanceof StringTag) {
                $tile = $pos->getWorld()->getTile($pos);
                if (!$tile instanceof SimpleTile) {
                    $tileinfo = new TileInfo($pos, ["id" => "simpleTile", "stacked" => 1]);
                    new SimpleTile($pos->getWorld(), $tileinfo);
                }
            }
        }

        $bBelow = $pos->getWorld()->getBlock($pos->floor()->down(1));
        if (!$this->util->checkBlock($bBelow) instanceof Block) {
            $event->cancel();
            $event->getPlayer()->sendMessage(Tf::RED . "You can not place blocks over an OreSpawner!");
        }
    }

    public function onPlayerInteract(PlayerInteractEvent $event): void
    {
        if ($this->cfg["stacking"] == false || $event->isCancelled()) {
            return;
        }

        $block = $event->getBlock();
        $pos = $block->getPosition();

        $item = $event->getItem();
        $player = $event->getPlayer();
        $blocks = [];
        foreach (array_values($this->plugin->getConfig()->get("ore-generator-blocks")) as $blockID) {
            array_push($blocks, $blockID);
        }

        if (in_array($event->getBlock()->getId(), $blocks)) {
            $tile = $pos->getWorld()->getTile($pos);
            if ($tile instanceof SimpleTile) {
                if ($player->getGamemode()->getEnglishName() !== "Creative") {
                    $stacked = $tile->getData("stacked")->getValue();
                    if (in_array($item->getId(), $blocks) && $item->getNamedTag()->getTag("orespawner") instanceof StringTag) {
                        if ($event->getBlock()->getId() == $item->getId()) {
                            if (!($stacked >= (int) $this->cfg["max"])) {
                                $event->cancel();
                                $tile->setData("stacked", $stacked + 1);
                                $item->setCount($item->getCount() - 1);
                                $player->getInventory()->setItem($player->getInventory()->getHeldItemIndex(), $item);
                                $player->sendMessage(str_replace("&", "§", $this->cfg["gen-added"] ?? "&aSuccessfully stacked a orespawner"));
                                return;
                            }
                            $player->sendMessage(str_replace("&", "§", $this->cfg["limit-reached"] ?? "&cYou can't stack anymore orespawners, you have reached the limit"));
                            return;
                        }
                        $player->sendMessage("§cPlease hold the right type of OreSpawner to stack");
                        return;
                    }
                    $player->sendMessage("§aThere are currently " . TF::YELLOW . $stacked . " §astacked OreSpawners");
                    return;
                }
                $player->sendMessage(TF::RED . "You can only using stacking system in Survival.");
                return;
            }
        }
    }

    public function onBlockBreak(BlockBreakEvent $event): void
    {
        if ($event->isCancelled()) {
            return;
        }

        $player = $event->getPlayer();

        $block = $event->getBlock();
        $pos = $block->getPosition();

        $bBelow = $pos->getWorld()->getBlock($pos->floor()->down(1));
        $blocks = [];
        foreach (array_values($this->plugin->getConfig()->get("ore-generator-blocks")) as $blockID) {
            array_push($blocks, $blockID);
        }

        if (in_array($event->getBlock()->getId(), $blocks)) {
            $tile = $pos->getWorld()->getTile($pos);
            if ($tile instanceof SimpleTile) {
                $type = $this->util->checkSpawner($block);
                if (!is_string($type)) {
                    return;
                }

                $count = $tile->getData("stacked")->getValue();
                $oreSpawner = $this->plugin->createOreSpawner($type, $count);

                $event->setDrops([$oreSpawner]);
            }
            return;
        }
        if (in_array($bBelow->getId(), $blocks)) {
            if ($this->cfg["drop-xp"] == false) {
                $event->setXpDropAmount(0);
            }
        }
    }
}
