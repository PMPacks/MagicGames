<?php


namespace blueturk\skyblock\listener;

use pocketmine\Server;
use pocketmine\block\Cake;
use pocketmine\block\Grass;
use pocketmine\world\World;
use pocketmine\item\ItemIds;
use pocketmine\item\SpawnEgg;
use pocketmine\player\Player;
use pocketmine\event\Listener;
use blueturk\skyblock\SkyBlock;
use pocketmine\block\ItemFrame;
use pocketmine\item\FlintSteel;
use pocketmine\item\TieredTool;
use pocketmine\item\LiquidBucket;
use pocketmine\item\PaintingItem;
use pocketmine\inventory\Inventory;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use blueturk\skyblock\managers\IslandManager;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\player\PlayerBucketEmptyEvent;

class IslandListener implements Listener
{
    public function onJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        $data = SkyBlock::getInstance()->getConfig();
        if ($data->getNested($player->getName() . ".island") !== null) {
            IslandManager::teleportToIsland($player);
        }
    }

    public function onQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();
        $data = SkyBlock::getInstance()->getConfig();

        // This is so as to prevent unintended chunk glitches or problems the next time players join.
        $defaultWorld = Server::getInstance()->getWorldManager()->getDefaultWorld();
        if ($defaultWorld instanceof World) {
            $player->teleport($defaultWorld->getSafeSpawn());
        }

        $partnerIslands = [];
        if ($data->getNested($player->getName()) !== null) {
            if ($data->getNested($player->getName() . ".island") !== null) {
                $world = Server::getInstance()->getWorldManager()->getWorldByName($player->getName());
                if (!$world instanceof World) {
                    return;
                }

                if (Server::getInstance()->getWorldManager()->isWorldLoaded($player->getName())) {
                    Server::getInstance()->getWorldManager()->unloadWorld($world);
                }
                if (SkyBlock::getInstance()->getConfig()->getNested($player->getName() . ".island" . ".other-partners") != null) {
                    foreach (SkyBlock::getInstance()->getConfig()->getNested($player->getName() . ".island" . ".other-partners") as $item => $value) {
                        $partnerIslands[] = $value;
                    }
                }
            } elseif (SkyBlock::getInstance()->getConfig()->getNested($player->getName() . ".partners") != null) {
                foreach (SkyBlock::getInstance()->getConfig()->getNested($player->getName() . ".partners") as $item => $value) {
                    $partnerIslands[] = $value;
                }
            }
        }

        foreach ($partnerIslands as $islandName) {
            $world = Server::getInstance()->getWorldManager()->getWorldByName($islandName);
            if ($world instanceof World && count($world->getPlayers()) === 0 && Server::getInstance()->getWorldManager()->isWorldLoaded($islandName)) {
                Server::getInstance()->getWorldManager()->unloadWorld($world);
            }
        }
    }

    /** 
     * @priority LOWEST 
     * @handleCancelled
     */
    public function onInteract(PlayerInteractEvent $event): void
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $block = $event->getBlock();

        $level = $player->getWorld()->getFolderName();
        $data = SkyBlock::getInstance()->getConfig();

        $event->cancel();

        $worlds = ["MagicGames", "Mining", "Arena"];
        foreach ($worlds as $world) {
            if ($level === $world) {
                if ($item instanceof LiquidBucket || $block instanceof Cake || $item instanceof PaintingItem || $item instanceof FlintSteel || $item instanceof SpawnEgg || $item->getId() === ItemIds::ARMOR_STAND || $block instanceof ItemFrame || ($item instanceof TieredTool && $block instanceof Grass)) {
                    $event->cancel();
                    return;
                }
                $event->uncancel();
                return;
            }
        }
        if ($data->getNested($level . ".island") != null) {
            if ($level === $player->getName()) {
                $event->uncancel();
                return;
            }
            if (Server::getInstance()->isOp($player->getName())) {
                $event->uncancel();
                return;
            }

            if (in_array($player->getName(), $data->getNested($level . ".island" . ".this-partners") ?? [])) {
                if ($data->getNested($level . ".island" . ".settings" . ".interact") === true) {
                    $event->uncancel();
                    return;
                }
                $event->cancel();
                $player->sendPopup(SkyBlock::BT_MARK . "cYour partner won't let you interact!");
                return;
            }
        }
    }

    /** 
     * @priority LOWEST 
     * @handleCancelled
     */
    public function onPlaced(BlockPlaceEvent $event): void
    {
        $player = $event->getPlayer();
        $level = $player->getWorld()->getFolderName();
        $data = SkyBlock::getInstance()->getConfig();

        $event->cancel();
        if ($data->getNested($level . ".island") != null) {
            if ($level === $player->getName()) {
                $event->uncancel();
                return;
            }
            if (Server::getInstance()->isOp($player->getName())) {
                $event->uncancel();
                return;
            }
            if (in_array($player->getName(), $data->getNested($level . ".island" . ".this-partners") ?? [])) {
                if ($data->getNested($level . ".island" . ".settings" . ".place") === true) {
                    $event->uncancel();
                    return;
                }
                $event->cancel();
                $player->sendPopup(SkyBlock::BT_MARK . "cYour partner won't let you!");
                return;
            }
        }
    }

    /** 
     * @priority LOWEST 
     * @handleCancelled
     */
    public function onBreak(BlockBreakEvent $event): void
    {
        $player = $event->getPlayer();
        $level = $player->getWorld()->getFolderName();
        $data = SkyBlock::getInstance()->getConfig();

        $event->cancel();
        if ($data->getNested($level . ".island") != null) {
            if ($level === $player->getName()) {
                $event->uncancel();
                $drops = $event->getDrops();
                foreach ($drops as $key => $drop) {
                    if ($player->getInventory()->canAddItem($drop)) {
                        $player->getInventory()->addItem($drop);
                        unset($drops[$key]);
                    } else {
                        $player->sendPopup("§l§eINVENTORY FULL");
                    }
                }
                $event->setDrops($drops);
                $xpDrops = $event->getXpDropAmount();
                $player->getXpManager()->addXp($xpDrops);
                $event->setXpDropAmount(0);
                return;
            }
            if (Server::getInstance()->isOp($player->getName())) {
                $event->uncancel();
                return;
            }

            if (in_array($player->getName(), $data->getNested($level . ".island" . ".this-partners") ?? [])) {
                if ($data->getNested($level . ".island" . ".settings" . ".break") === true) {
                    $event->uncancel();
                    $drops = $event->getDrops();
                    foreach ($drops as $key => $drop) {
                        if ($player->getInventory()->canAddItem($drop)) {
                            $player->getInventory()->addItem($drop);
                            unset($drops[$key]);
                        } else {
                            $player->sendPopup("§l§eINVENTORY FULL");
                        }
                    }
                    $event->setDrops($drops);
                    $xpDrops = $event->getXpDropAmount();
                    $player->getXpManager()->addXp($xpDrops);
                    $event->setXpDropAmount(0);
                    return;
                }
                $event->cancel();
                $player->sendPopup(SkyBlock::BT_MARK . "cYour partner won't let you!");
                return;
            }
        }
    }

    public function onPickingUp(EntityItemPickupEvent $event): void
    {
        $inventory = $event->getInventory();
        if (!$inventory instanceof Inventory) {
            return;
        }

        $viewers = $inventory->getViewers();
        foreach ($viewers as $player) {
            $level = $player->getWorld();
            $levelName = $level->getFolderName();
            $data = SkyBlock::getInstance()->getConfig();

            $worlds = ["MagicGames", "Mining"];
            foreach ($worlds as $world) {
                if ($levelName === $world) {
                    $event->uncancel();
                    return;
                }
            }
            if ($data->getNested($levelName . ".island") != null) {
                if ($levelName === $player->getName()) {
                    $event->uncancel();
                    return;
                }
                if (Server::getInstance()->isOp($player->getName())) {
                    $event->uncancel();
                    return;
                }

                if (in_array($player->getName(), $data->getNested($levelName . ".island" . ".this-partners") ?? [])) {
                    if ($data->getNested($levelName . ".island" . ".settings" . ".picking-up") === true) {
                        $event->uncancel();
                        return;
                    }
                    $event->cancel();
                    $player->sendPopup(SkyBlock::BT_MARK . "cYour partner won't let you!");
                    return;
                }
            }
            $event->cancel();
        }
    }

    public function onPlayerBucketEmpty(PlayerBucketEmptyEvent $event): void
    {
        $player = $event->getPlayer();

        $worlds = ["MagicGames", "Mining", "Arena"];
        foreach ($worlds as $world) {
            if ($player->getWorld()->getFolderName() === $world) {
                $event->cancel();
                return;
            }
        }
    }

    public function onMove(PlayerMoveEvent $event): void
    {
        $player = $event->getPlayer();
        $level = $player->getWorld()->getFolderName();
        $data = SkyBlock::getInstance()->getConfig();
        if ($data->getNested($level . ".island") != null) {
            if (in_array($player->getName(), $data->getNested($level . ".island" . ".banneds") ?? [])) {
                if (!Server::getInstance()->isOp($player->getName())) {
                    $defaultWorld = Server::getInstance()->getWorldManager()->getDefaultWorld();
                    if (!$defaultWorld instanceof World) {
                        return;
                    }

                    $player->teleport($defaultWorld->getSpawnLocation());
                    $player->sendPopup(SkyBlock::BT_MARK . "cYou are banned on this island!");
                }
            }
        }
    }

    public function onDamage(EntityDamageEvent $event): void
    {
        $player = $event->getEntity();
        if ($player instanceof Player) {
            $level = $player->getWorld()->getFolderName();
            if ($level === $player->getName()) {
                if ($event->getCause() === EntityDamageEvent::CAUSE_VOID) {
                    $world = Server::getInstance()->getWorldManager()->getWorldByName($player->getName());
                    if (!$world instanceof World) {
                        return;
                    }

                    $event->cancel();
                    $player->teleport($world->getSpawnLocation());
                    if ($player->getXpManager()->getXpLevel() >= 7) {
                        $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - 7);
                        $player->sendMessage("§8» §cUnfortunately, you died in adana and lost §7(%3) XP §c experience level.");
                    }
                }
                /*if ($event->getCause() === EntityDamageEvent::CAUSE_FALL) {
                    $event->cancel();
                }*/
                $event->cancel();
                return;
            }
            if (SkyBlock::getInstance()->getConfig()->getNested($player->getName() . ".island") != null) {
                $event->cancel();
            }
        }
    }
}
