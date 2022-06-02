<?php

declare(strict_types=1);

namespace BhawaniSingh\HCMinion\entities;

use pocketmine\nbt\NBT;
use pocketmine\block\Air;
use pocketmine\item\Item;
use pocketmine\item\Armor;
use muqsit\invmenu\InvMenu;
use pocketmine\block\Block;
use pocketmine\color\Color;
use pocketmine\world\World;
use pocketmine\entity\Human;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use AndreasHGK\SellAll\SellAll;
use pocketmine\nbt\tag\ListTag;
use pocketmine\timings\Timings;
use pocketmine\item\ItemFactory;
use pocketmine\utils\TextFormat;
use pocketmine\item\VanillaItems;
use onebone\economyapi\EconomyAPI;
use pocketmine\block\BlockToolType;
use pocketmine\block\VanillaBlocks;
use pocketmine\inventory\Inventory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\block\BlockLegacyIds;
use BhawaniSingh\HCMinion\utils\Utils;
use BhawaniSingh\HCMinion\BetterMinion;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\item\StringToItemParser;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\event\entity\EntityDamageEvent;
use BhawaniSingh\HCMinion\minions\MinionUpgrade;
use pocketmine\world\particle\BlockBreakParticle;
use BhawaniSingh\HCMinion\minions\MinionInformation;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelEvent;
use BhawaniSingh\HCMinion\entities\inventory\MinionInventory;
use muqsit\invmenu\transaction\DeterministicInvMenuTransaction;

abstract class MinionEntity extends Human
{
    public const ACTION_INVENTORY_FULL = "§cINVENTORY FULL";
    public const ACTION_IDLE = "§gIDLE";
    public const ACTION_TURNING = "§gTURNING";
    public const ACTION_WORKING = "§gWORKING";

    protected MinionInformation $minionInformation;
    protected MinionInventory $minionInventory;

    public string $currentAction = self::ACTION_IDLE;

    public int $queueNumber;
    public int $inQueueTime = 0;

    public bool $isViewingInv = false;
    public bool $isWorking = false;

    public Block $target;

    private float $money = 0;

    protected EnchantmentInstance $fakeEnchant;

    /** @var float */
    protected $gravity = 0;

    public function saveNBT(): CompoundTag
    {
        $nbt = parent::saveNBT();

        if (isset($this->minionInformation)) {
            $nbt->setTag("MinionInformation", $this->minionInformation->nbtSerialize());
        }
        if (isset($this->minionInventory)) {
            $nbt->setTag('MinionInventory', new ListTag(
                array_map(
                    fn (Item $item) => $item->nbtSerialize(),
                    $this->minionInventory->getContents()
                ),
                NBT::TAG_Compound
            ));
        }
        if (isset($this->money)) {
            $nbt->setFloat('Money', $this->money);
        }

        return $nbt;
    }

    public function attack(EntityDamageEvent $source): void
    {
        if ($source instanceof EntityDamageByEntityEvent) {
            $damager = $source->getDamager();
            if ($damager instanceof Player) {
                if (isset(BetterMinion::getInstance()->isRemove[$damager->getName()])) {
                    $damager->sendMessage(' §eSuccessfully removed ' . $this->getMinionInformation()->getOwner() . "'s minion");
                    $this->destroy();
                    return;
                }
                if ($damager->getName() === $this->getMinionInformation()->getOwner() || $this->server->isOp($damager->getName())) {
                    $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
                    $menu->setName("§r§l§eMINION INVENTORY");

                    $menu->setListener(InvMenu::readonly(function (DeterministicInvMenuTransaction $transaction): void {
                        $player = $transaction->getPlayer();
                        $itemClicked = $transaction->getItemClicked();
                        $action = $transaction->getAction();

                        switch ($action->getSlot()) {
                            case 6:
                                break;
                            case 19:
                                // Auto Smelter
                                if ($this->canUseAutoSmelt()) {
                                    $player->removeCurrentWindow();
                                    if (!$this->getMinionInformation()->getUpgrade()->isAutoSmelt()) {
                                        $this->getMinionInformation()->getUpgrade()->setAutoSmelt(true);

                                        $player->sendMessage(" §eAuto Smelter upgrade enabled!");
                                        break;
                                    }
                                    $this->getMinionInformation()->getUpgrade()->setAutoSmelt(false);

                                    $player->sendMessage(" §eAuto Smelter upgrade disabled!");
                                    break;
                                }
                                $player->sendMessage(" §eYou need to upgrade your minion to level §6" . MinionUpgrade::AUTO_SMELT_LEVEL . "§e to use the Auto Smelter!");
                                break;
                            case 28:
                                // Auto Sell
                                if ($this->canUseAutoSell()) {
                                    $player->removeCurrentWindow();
                                    if (!$this->getMinionInformation()->getUpgrade()->isAutoSell()) {
                                        $this->getMinionInformation()->getUpgrade()->setAutoSell(true);

                                        $player->sendMessage(" §eAuto Sell upgrade enabled!");
                                        break;
                                    } elseif ($this->money > 0) {
                                        EconomyAPI::getInstance()->addMoney($player, $this->money);
                                        $player->sendMessage(" §eSuccessfully withdrew an amount of§6 $this->money §efrom the minion!");
                                        $this->money = 0;
                                        break;
                                    }
                                    $this->getMinionInformation()->getUpgrade()->setAutoSell(false);

                                    $player->sendMessage(" §eAuto Sell upgrade disabled!");
                                    break;
                                }
                                $player->sendMessage(" §eYou need to upgrade your minion to level §6" . MinionUpgrade::AUTO_SELL_LEVEL . "§e to use the Auto Sell!");
                                break;
                            case 37:
                                // Super Compacter
                                if ($this->canUseCompacter()) {
                                    $player->removeCurrentWindow();
                                    if (!$this->getMinionInformation()->getUpgrade()->isSuperCompacter()) {
                                        $this->getMinionInformation()->getUpgrade()->setSuperCompacter(true);

                                        $player->sendMessage(" §eSuper Compacter upgrade enabled!");
                                        break;
                                    }
                                    $this->getMinionInformation()->getUpgrade()->setSuperCompacter(false);

                                    $player->sendMessage(" §eSuper Compacter upgrade disabled!");
                                    break;
                                }
                                $player->sendMessage(" §eYou need to upgrade your minion to level §6" . MinionUpgrade::SUPER_COMPACTER_LEVEL . "§e to use the Super Compacter!");
                                break;
                            case 46:
                                // Super Expander
                                if ($this->canUseExpander()) {
                                    $player->removeCurrentWindow();
                                    if (!$this->getMinionInformation()->getUpgrade()->isSuperExpander()) {
                                        $this->getMinionInformation()->getUpgrade()->setSuperExpander(true);

                                        $player->sendMessage(" §eSuper Expander upgrade enabled!");
                                        break;
                                    }
                                    $this->getMinionInformation()->getUpgrade()->setSuperExpander(false);

                                    $player->sendMessage(" §eSuper Expander upgrade disabled!");
                                    break;
                                }
                                $player->sendMessage(" §eYou need to upgrade your minion to level §6" . MinionUpgrade::SUPER_EXPANDER_LEVEL . "§e to use the Super Expander!");
                                break;
                            case 48:
                                // Collect All
                                foreach ($this->getMinionInventory()->getContents() as $slot => $item) {
                                    if ($player->getInventory()->canAddItem($item)) {
                                        $player->getInventory()->addItem($item);
                                        $this->getMinionInventory()->setItem($slot, VanillaBlocks::AIR()->asItem());
                                        continue;
                                    }
                                    $player->sendMessage(' §eYour Inventory Is Full, Empty It Before Making A Transaction');
                                    break;
                                }
                                break;
                            case 50:
                                // Level Up
                                $player->removeCurrentWindow();
                                if ($this->getMinionInformation()->getLevel() < 15) {
                                    $playerMoney = EconomyAPI::getInstance()->myMoney($player);
                                    if (is_bool($playerMoney)) {
                                        break;
                                    }

                                    if ($playerMoney - $this->getLevelUpCost() >= 0) {
                                        EconomyAPI::getInstance()->reduceMoney($player, $this->getLevelUpCost());
                                        $this->getMinionInformation()->incrementLevel();
                                        $player->sendMessage(' §eYour Minion Has Been Upgraded To Level§6 ' . TextFormat::GOLD . Utils::getRomanNumeral($this->getMinionInformation()->getLevel()));
                                        $this->getMinionInventory()->setSize($this->getMinionInformation()->getLevel());
                                        $this->stopWorking();
                                        break;
                                    }
                                    $player->sendMessage(" §eYou Don't Have Enough Money For Upgrade Minion");
                                    break;
                                }
                                $player->sendMessage(' §eYour Minion Has Reached The Maximum Level');
                                break;
                            case 52:
                                // Remove Minion
                                $player->removeCurrentWindow();
                                $this->destroy();
                                break;
                            default:
                                for ($i = 0; $i <= 15; ++$i) {
                                    if ($i > $this->getMinionInformation()->getLevel() - 1) {
                                        continue;
                                    }
                                    $slot = (int) (21 + ($i % 5) + (9 * floor($i / 5)));
                                    if ($action->getSlot() === $slot) {
                                        if ($player->getInventory()->canAddItem($itemClicked)) {
                                            $player->getInventory()->addItem($itemClicked);
                                            $remaining = $itemClicked->getCount();
                                            /** @var Item $item */
                                            foreach (array_reverse($this->getMinionInventory()->all($itemClicked), true) as $index => $item) {
                                                $itemCount = $item->getCount();
                                                $newCount = max($itemCount - $remaining, 0);

                                                $this->getMinionInventory()->setItem($index, $item->setCount($newCount > 64 ? 64 : $newCount));
                                                $remaining -= $itemCount;
                                                if ($remaining === 0) {
                                                    break;
                                                }
                                            }
                                        } else {
                                            $player->removeCurrentWindow();
                                            $player->sendMessage(' §eYour Inventory Is Full, Empty It Before Making A Transaction');
                                        }
                                    }
                                }
                                break;
                        }
                        for ($i = 0; $i < 15; ++$i) {
                            $action->getInventory()->setItem((int) (21 + ($i % 5) + (9 * floor($i / 5))), $this->getMinionInventory()->slotExists($i) ? $this->getMinionInventory()->getItem($i) : ItemFactory::getInstance()->get(1080, 0, 1)->setCustomName(TextFormat::RESET . TextFormat::GOLD . 'Unlock At Level ' . TextFormat::AQUA . Utils::getRomanNumeral(($i + 1))));
                        }
                    }));
                    $menu->setInventoryCloseListener(function (Player $player, Inventory $inventory): void {
                        $this->isViewingInv = false;
                    });
                    $menu->send($damager, null, function () use ($menu): void {
                        $this->isViewingInv = true;

                        $menu->getInventory()->setContents(array_fill(0, 54, ItemFactory::getInstance()->get(1081, 0, 1)->setCustomName(TextFormat::RESET)));
                        for ($i = 0; $i < 15; ++$i) {
                            $menu->getInventory()->setItem((int) (21 + ($i % 5) + (9 * floor($i / 5))), $this->getMinionInventory()->slotExists($i) ? $this->getMinionInventory()->getItem($i) : ItemFactory::getInstance()->get(1080, 0, 1)->setCustomName(TextFormat::RESET . TextFormat::GOLD . 'Unlock At Level ' . TextFormat::AQUA . Utils::getRomanNumeral(($i + 1))));
                        }

                        $types = ['Mining', 'Farming', 'Lumberjack', 'Slaying', 'Fishing'];
                        $menu->getInventory()->setItem(4, ItemFactory::getInstance()->get(ItemIds::SKULL, 3)->setCustomName(TextFormat::RESET . TextFormat::BOLD . TextFormat::YELLOW . $this->getMinionInformation()->getType()->getTargetName() . ' Minion ' . Utils::getRomanNumeral($this->getMinionInformation()->getLevel()))->setLore([
                            '§r§6Type: ' . TextFormat::WHITE . $types[$this->getMinionInformation()->getType()->getActionType()],
                            '§r§6Target: ' . TextFormat::WHITE . $this->getMinionInformation()->getType()->getTargetName(),
                            '§r§6Level: ' . TextFormat::WHITE . $this->getMinionInformation()->getLevel(),
                            '§r§6Resources Collected: ' . TextFormat::WHITE . $this->getMinionInformation()->getResourcesCollected(),
                        ]));

                        // Auto Smelter
                        if ($this->canUseAutoSmelt()) {
                            $furnaceItem = VanillaBlocks::FURNACE()->asItem();
                            $menu->getInventory()->setItem(19, $furnaceItem->setCustomName('§r§l§eAuto Smelter §l§7(' . ($this->getMinionInformation()->getUpgrade()->isAutoSmelt() ? "§r§l§aEnabled" : "§r§l§cDisabled") . '§l§7)')->setLore(["§r§7Automatically Smelts Items That\n§r§7The Minion Produces"]));
                        } else {
                            $menu->getInventory()->setItem(19, ItemFactory::getInstance()->get(1101, 0, 1)->setCustomName("§r§cYou need to upgrade your minion to level " . MinionUpgrade::AUTO_SMELT_LEVEL . " to use the Auto Smelter!"));
                        }

                        // Auto Seller
                        if ($this->canUseAutoSell()) {
                            $hopper = VanillaBlocks::HOPPER()->asItem();
                            $menu->getInventory()->setItem(28, $hopper->setCustomName('§r§l§eAuto Seller §l§7(' . ($this->getMinionInformation()->getUpgrade()->isAutoSell() ? "§r§l§aEnabled" : "§r§l§cDisabled") . '§l§7)')->setLore(["§r§7Sell Automatically Resources When\n§r§7The Minion's Storage Is Full", "§r§l§dTotal Money: §f" . $this->money]));
                        } else {
                            $menu->getInventory()->setItem(28, ItemFactory::getInstance()->get(1101, 0, 1)->setCustomName("§r§cYou need to upgrade your minion to level " . MinionUpgrade::AUTO_SELL_LEVEL . " to use the Auto Seller!"));
                        }

                        // Super Compacter
                        if ($this->canUseCompacter()) {
                            $dispenser = ItemFactory::getInstance()->get(23, 0, 1);
                            $menu->getInventory()->setItem(37, $dispenser->setCustomName('§r§l§eCompacter §l§7(' . ($this->getMinionInformation()->getUpgrade()->isSuperCompacter() ? "§r§l§aEnabled" : "§r§l§cDisabled") . '§l§7)')->setLore(["§r§7Automatically Convert Items Into\n§r§7Block From This Upgrade"]));
                        } else {
                            $menu->getInventory()->setItem(37, ItemFactory::getInstance()->get(1101, 0, 1)->setCustomName("§r§cYou need to upgrade your minion to level " . MinionUpgrade::SUPER_COMPACTER_LEVEL . " to use the Super Compacter!"));
                        }

                        // Super Expander
                        if ($this->canUseExpander()) {
                            $commandBlock = ItemFactory::getInstance()->get(137, 0, 1);
                            $commandBlock->addEnchantment($this->fakeEnchant);

                            $menu->getInventory()->setItem(46, $commandBlock->setCustomName('§r§l§eExpander §l§7(' . ($this->getMinionInformation()->getUpgrade()->isSuperExpander() ? "§r§l§aEnabled" : "§r§l§cDisabled") . '§l§7)')->setLore(["§r§7Increases The Minion Range\n§r§7By One Block To High Blocks"]));
                        } else {
                            $menu->getInventory()->setItem(46, ItemFactory::getInstance()->get(1101, 0, 1)->setCustomName("§r§cYou need to upgrade your minion to level " . MinionUpgrade::SUPER_EXPANDER_LEVEL . " to use the Super Expander!"));
                        }

                        $menu->getInventory()->setItem(48, ItemFactory::getInstance()->get(1103, 0, 1)->setCustomName("§r§l§eCOLLECT ITEMS\n\n§r§7Click To Collect All Items From\n§r§7Your Minion Inventory To Your\n§r§7Inventory.\n\n§r§dClick To Collect"));
                        $menu->getInventory()->setItem(50, ItemFactory::getInstance()->get(1102, 0, 1)->setCustomName("§r§l§eUPGRADE MINION\n\n§r§7Click To Upgrade Your Minion\n§r§7Level 1 To High Levels For\n§r§7Open Inventory Slots.\n\n§r§dClick To Upgrade")->setLore([$this->getMinionInformation()->getLevel() < 15 ? "§r§l§6AMOUNT: §r§e" . TextFormat::GREEN . $this->getLevelUpCost() . "$" : TextFormat::LIGHT_PURPLE . "Reached Max Level"]));
                        $menu->getInventory()->setItem(52, ItemFactory::getInstance()->get(1104, 0, 1)->setCustomName("§r§l§ePICKUP MINION\n\n§r§7Click To Pickup Your Minion\n§r§7To Move In New Location\n\n§r§dClick To Pickup"));
                        $menu->getInventory()->setItem(6, ItemFactory::getInstance()->get(1084, 0, 1)->setCustomName("§r§d§lRECIPES\n\n§7More Minion Recipes"));
                        $menu->getInventory()->setItem(5, ItemFactory::getInstance()->get(ItemIds::ENDER_EYE)->setCustomName("§r§l§eTOTAL UPGRADE AMOUNT\n§r§aLevel 1: §r§d0\n§r§aLevel 2: §r§d1000$\n§r§aLevel 3: §r§d2000$\n§r§aLevel 4: §r§d4000$\n§r§aLevel 5: §r§d8000$\n§r§aLevel 6: §r§d12000$\n§r§aLevel 7: §r§d15000$\n§r§aLevel 8: §r§d17500$\n§r§aLevel 9: §r§d20000$\n§r§aLevel 10: §r§d22000$\n§r§aLevel 11: §r§d25000$\n§r§aLevel 12: §r§d27000$\n§r§aLevel 13: §r§d30000$\n§r§aLevel 14: §r§d35000$\n§r§aLevel 15: §r§d40000$"));
                    });
                }
            }
        }
        $source->cancel();
    }

    /**
     * entityBaseTick
     *
     * @see PLEASE READ MESSAGE IN METHOD BEFORE DOING ANYTHING TO THIS!
     */
    public function entityBaseTick(int $tickDiff = 1): bool
    {
        // █▀█ █░░ █▀▀ ▄▀█ █▀ █▀▀   █▀█ █▀▀ ▄▀█ █▀▄
        // █▀▀ █▄▄ ██▄ █▀█ ▄█ ██▄   █▀▄ ██▄ █▀█ █▄▀
        // 
        // In order to reduce the amount of lag made by minions, I have decided to downgrade the tick rate of the minions.
        // The entityBaseTick method is called every tick, and it is the method that is responsible for the most of the lag.
        // I have made it such that the tick rate is increased by a factor of 20.
        // If you are reading this, please do not do anything to this method.
        // 
        // The minion has also been stripped off of non necessary methods.

        $hasUpdate = false;

        $this->ticksLived += $tickDiff;
        Timings::$livingEntityBaseTick->startTiming();

        // █▀▀ █▄░█ ▀█▀ █ ▀█▀ █▄█
        // ██▄ █░▀█ ░█░ █ ░█░ ░█░

        $changedProperties = $this->getDirtyNetworkData();
        if (count($changedProperties) > 0) {
            $this->sendData(null, $changedProperties);
            $this->getNetworkProperties()->clearDirtyProperties();
        }

        if ($this->location->y <= World::Y_MIN - 16 && $this->isAlive()) {
            $this->destroy();
            $hasUpdate = true;
        }

        // █▀▄▀█ █ █▄░█ █ █▀█ █▄░█  █▀▀ █▄░█ ▀█▀ █ ▀█▀ █▄█
        // █░▀░█ █ █░▀█ █ █▄█ █░▀█  ██▄ █░▀█ ░█░ █ ░█░ ░█░

        if (!isset($this->queueNumber) || !isset(BetterMinion::$minionQueue[$this->queueNumber])) {
            if (!$this->isInsideOfSolid() && $this->checkFull() && !$this->closed && !$this->isFlaggedForDespawn() && isset($this->minionInformation) && !$this->isViewingInv) {
                $this->queueNumber = BetterMinion::$queueNumber++;
                $this->inQueueTime = 0;
                BetterMinion::$minionQueue[$this->queueNumber] = $this;
                if (count(BetterMinion::$minionQueue) > BetterMinion::QUEUE_CYCLE) {
                    $this->setNameTag("§l§6" . strtoupper($this->getMinionInformation()->getType()->getTargetName()) . "§r\n§e" . $this->getMinionInformation()->getOwner() . "'s Minion §r(§gQUEUE-" . (count(BetterMinion::$minionQueue) - BetterMinion::QUEUE_CYCLE) . "§r)");
                    $this->setNameTagAlwaysVisible(false);
                }
            }
            // In the case there is a mismatch, it will reset the minion's state.
        } elseif (!$this->isInventoryFull() && $this->inQueueTime++ > 20) {
            BetterMinion::getInstance()->getLogger()->info("Minion timed out in queue. Removing " . $this->getMinionInformation()->getOwner() . " from the queue.");
            if (isset(BetterMinion::$minionQueue[$this->queueNumber])) {
                unset(BetterMinion::$minionQueue[$this->queueNumber]);
            }
            unset($this->queueNumber);
            $this->inQueueTime = 0;
        }

        Timings::$livingEntityBaseTick->stopTiming();

        return $hasUpdate;
    }

    public function canBeCollidedWith(): bool
    {
        return false;
    }

    public function addEffect(EffectInstance $effect): bool
    {
        return false;
    }

    public function getMinionInformation(): MinionInformation
    {
        return $this->minionInformation;
    }

    public function getMinionInventory(): MinionInventory
    {
        return $this->minionInventory;
    }

    public function initEntity(CompoundTag $nbt): void
    {
        parent::initEntity($nbt);
        $this->setScale(0.6);
        $this->setImmobile();

        $listTag = $nbt->getTag('MinionInformation');
        if (!$listTag instanceof ListTag) {
            return;
        }

        $this->minionInformation = MinionInformation::nbtDeserialize($listTag);
        $this->minionInventory = new MinionInventory(15);
        $this->minionInventory->setSize($this->minionInformation->getLevel());
        $this->money = $nbt->getFloat('Money', 0);

        /** @phpstan-ignore-next-line */
        $this->fakeEnchant = new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(BetterMinion::FAKE_ENCH_ID));
        $this->target = VanillaBlocks::AIR();

        $invTag = $nbt->getTag('MinionInventory');
        if ($invTag instanceof ListTag) {
            /** @var array<CompoundTag> $itemTags */
            $itemTags = $invTag->getValue();
            $this->minionInventory->setContents(array_map(
                fn (CompoundTag $itemTag) => Item::nbtDeserialize($itemTag),
                $itemTags
            ));
        }

        $colorData = [$this->getMinionInformation()->getType()->getTargetId(), $this->getMinionInformation()->getType()->getTargetMeta()];
        $color = match ($colorData) {
            [BlockLegacyIds::COBBLESTONE, 0] => new Color(192, 192, 192),
            [BlockLegacyIds::LOG, 0] => new Color(255, 255, 0),
            [BlockLegacyIds::LOG, 1] => new Color(0, 204, 204),
            [BlockLegacyIds::LOG, 2] => new Color(224, 224, 224),
            [BlockLegacyIds::LOG, 3] => new Color(51, 255, 51),
            [BlockLegacyIds::LOG2, 0] => new Color(153, 51, 255),
            [BlockLegacyIds::LOG2, 1] => new Color(255, 0, 0),
            [BlockLegacyIds::CARROT_BLOCK, 7] => new Color(255, 178, 102),
            [BlockLegacyIds::POTATO_BLOCK, 7] => new Color(204, 102, 0),
            [BlockLegacyIds::WHEAT_BLOCK, 7] => new Color(255, 255, 105),
            [BlockLegacyIds::MELON_BLOCK, 0] => new Color(102, 255, 102),
            [BlockLegacyIds::PUMPKIN, 0] => new Color(255, 178, 102),
            [BlockLegacyIds::CLAY_BLOCK, 0] => new Color(204, 204, 255),
            [BlockLegacyIds::EMERALD_ORE, 0] => new Color(102, 255, 102),
            [BlockLegacyIds::DIAMOND_ORE, 0] => new Color(102, 255, 255),
            [BlockLegacyIds::NETHER_QUARTZ_ORE, 0] => new Color(204, 255, 255),
            [BlockLegacyIds::GOLD_ORE, 0] => new Color(255, 255, 51),
            [BlockLegacyIds::IRON_ORE, 0] => new Color(192, 192, 192),
            [BlockLegacyIds::COAL_ORE, 0] => new Color(96, 96, 96),
            [BlockLegacyIds::LAPIS_ORE, 0] => new Color(51, 153, 255),
            [BlockLegacyIds::REDSTONE_ORE, 0] => new Color(255, 102, 102),
            [BlockLegacyIds::END_STONE, 0] => new Color(255, 255, 204),
            [BlockLegacyIds::NETHERRACK, 0] => new Color(204, 0, 0),
            [BlockLegacyIds::SNOW_BLOCK, 0] => new Color(224, 224, 224),
            [BlockLegacyIds::SAND, 0] => new Color(255, 204, 153),
            [BlockLegacyIds::OBSIDIAN, 0] => new Color(64, 64, 64),
            default => new Color(192, 192, 192)
        };
        $armor2 = ItemFactory::getInstance()->get(299, 0, 1);
        $armor2 instanceof Armor ? $armor2->setCustomColor($color) : null;
        $armor3 = ItemFactory::getInstance()->get(300, 0, 1);
        $armor3 instanceof Armor ? $armor3->setCustomColor($color) : null;
        $armor4 = ItemFactory::getInstance()->get(301, 0, 1);
        $armor4 instanceof Armor ? $armor4->setCustomColor($color) : null;
        $this->getArmorInventory()->setChestplate($armor2);
        $this->getArmorInventory()->setLeggings($armor3);
        $this->getArmorInventory()->setBoots($armor4);

        $tool = BetterMinion::getInstance()->getConfig()->getNested('tool.tier', 'diamond');
        $isNetheriteTool = $tool === 'Netherite';
        if (($item = $this->getTool($tool, $isNetheriteTool)) instanceof Item) {
            $this->getInventory()->setItemInHand($item);
        }
    }

    public function getSmeltedTarget(): ?Item
    {
        $contents = file_get_contents(BetterMinion::getInstance()->getDataFolder() . 'smelts.json');
        if (!is_string($contents)) {
            return null;
        }

        $smeltedItems = json_decode($contents, true);
        foreach ($smeltedItems as $input => $output) {
            $realInput = StringToItemParser::getInstance()->parse($input);
            $realOutput = StringToItemParser::getInstance()->parse($output);
            foreach ($this->getRealDrops() as $drop) {
                if ($realInput instanceof Item && $realOutput instanceof Item) {
                    if ($realInput->equals($drop, true)) {
                        return $realOutput;
                    }
                }
            }
        }
        return null;
    }

    public function compactItems(): ?Item
    {
        $contents = file_get_contents(BetterMinion::getInstance()->getDataFolder() . 'compacts.json');
        if (!is_string($contents)) {
            return null;
        }

        foreach ($this->getMinionInventory()->getContents() as $index => $item) {
            if ($item->getCount() > 9) {
                $compactItems = json_decode($contents, true);
                foreach ($compactItems as $input => $output) {
                    $realInput =  StringToItemParser::getInstance()->parse($input) ?? LegacyStringToItemParser::getInstance()->parse($input);
                    $realOutput = StringToItemParser::getInstance()->parse($output) ?? LegacyStringToItemParser::getInstance()->parse($output);

                    if ($item->equals($realInput) && $this->getMinionInventory()->canAddItem($realOutput)) {
                        $item->setCount($item->getCount() - 9);
                        $this->getMinionInventory()->setItem($index, $item);
                        $this->getMinionInventory()->addItem($realOutput);
                        return $item;
                    }
                }
            }
        }
        return null;
    }

    public function getTargetDrops(): array
    {
        $drops = $this->getRealDrops();
        if ($this->getMinionInformation()->getUpgrade()->isAutoSmelt()) {
            $drops = [$this->getSmeltedTarget()];
        }
        return $drops;
    }

    public function updateTarget(): void
    {
    }

    abstract public function getTarget(): void;

    public function checkTarget(): bool
    {
        return $this->target instanceof Air || ($this->target->getId() === $this->getMinionInformation()->getType()->getTargetId() && $this->target->getMeta() === $this->getMinionInformation()->getType()->getTargetMeta());
    }

    public function startWorking(): bool
    {
        $this->getWorld()->addParticle($this->target->getPosition()->add(0.5, 0.5, 0.5), new BlockBreakParticle($this->target));
        $this->getWorld()->setBlock($this->target->getPosition(), $this->target instanceof Air ? $this->getMinionInformation()->getType()->toBlock() : VanillaBlocks::AIR(), false);
        if (!$this->target instanceof Air) {
            $drops = $this->getTargetDrops();
            foreach ($drops as $drop) {
                if ($drop instanceof Item) {
                    for ($i = 1; $i <= $drop->getCount(); ++$i) {
                        $thing = ItemFactory::getInstance()->get($drop->getId(), $drop->getMeta());
                        if ($this->getMinionInventory()->canAddItem($thing)) {
                            $this->getMinionInventory()->addItem($thing);
                            $this->getMinionInformation()->incrementResourcesCollected();

                            if ($this->getMinionInformation()->getUpgrade()->isSuperCompacter()) {
                                $this->compactItems();
                            }
                        }
                    }
                }
            }
            return true;
        }
        return false;
    }

    public function stopWorking(): void
    {
        $this->currentAction = self::ACTION_IDLE;

        if ($this->broadcastPlaceBreak()) {
            $this->getWorld()->broadcastPacketToViewers($this->getPosition(), LevelEventPacket::create(LevelEvent::BLOCK_STOP_BREAK, 0, $this->target->getPosition()));
        }
        $this->isWorking = false;
    }

    public function isInventoryFull(): bool
    {
        $full = true;
        $drops = $this->getTargetDrops();

        foreach ($drops as $item) {
            if ($item instanceof Item) {
                if ($this->getMinionInventory()->canAddItem($item)) {
                    $full = false;
                    break;
                }
            }
        }
        return $full;
    }

    public function destroy(): void
    {
        if ($this->broadcastPlaceBreak()) {
            $this->getWorld()->broadcastPacketToViewers($this->getPosition(), LevelEventPacket::create(LevelEvent::BLOCK_STOP_BREAK, 0, $this->target->getPosition()));
        }
        foreach ($this->getMinionInventory()->getContents() as $content) {
            $this->getWorld()->dropItem($this->getPosition(), $content);
        }

        $minionItemData = [$this->getMinionInformation()->getType()->getTargetId(), $this->getMinionInformation()->getType()->getTargetMeta()];
        $minionItem = match ($minionItemData) {
            [BlockLegacyIds::COBBLESTONE, 0] => ItemFactory::getInstance()->get(1110, 0, 1),
            [BlockLegacyIds::LOG, 0] => ItemFactory::getInstance()->get(1121, 0, 1),
            [BlockLegacyIds::LOG, 1] => ItemFactory::getInstance()->get(1129, 0, 1),
            [BlockLegacyIds::LOG, 2] => ItemFactory::getInstance()->get(1106, 0, 1),
            [BlockLegacyIds::LOG, 3] => ItemFactory::getInstance()->get(1117, 0, 1),
            [BlockLegacyIds::LOG2, 0] => ItemFactory::getInstance()->get(1105, 0, 1),
            [BlockLegacyIds::LOG2, 1] => ItemFactory::getInstance()->get(1111, 0, 1),
            [BlockLegacyIds::CARROT_BLOCK, 7] => ItemFactory::getInstance()->get(1107, 0, 1),
            [BlockLegacyIds::POTATO_BLOCK, 7] => ItemFactory::getInstance()->get(1123, 0, 1),
            [BlockLegacyIds::WHEAT_BLOCK, 7] => ItemFactory::getInstance()->get(1130, 0, 1),
            [BlockLegacyIds::MELON_BLOCK, 0] => ItemFactory::getInstance()->get(1119, 0, 1),
            [BlockLegacyIds::PUMPKIN, 0] => ItemFactory::getInstance()->get(1124, 0, 1),
            [BlockLegacyIds::CLAY_BLOCK, 0] => ItemFactory::getInstance()->get(1108, 0, 1),
            [BlockLegacyIds::EMERALD_ORE, 0] => ItemFactory::getInstance()->get(1113, 0, 1),
            [BlockLegacyIds::DIAMOND_ORE, 0] => ItemFactory::getInstance()->get(1112, 0, 1),
            [BlockLegacyIds::NETHER_QUARTZ_ORE, 0] => ItemFactory::getInstance()->get(1125, 0, 1),
            [BlockLegacyIds::GOLD_ORE, 0] => ItemFactory::getInstance()->get(1115, 0, 1),
            [BlockLegacyIds::IRON_ORE, 0] => ItemFactory::getInstance()->get(1116, 0, 1),
            [BlockLegacyIds::COAL_ORE, 0] => ItemFactory::getInstance()->get(1109, 0, 1),
            [BlockLegacyIds::LAPIS_ORE, 0] => ItemFactory::getInstance()->get(1118, 0, 1),
            [BlockLegacyIds::REDSTONE_ORE, 0] => ItemFactory::getInstance()->get(1126, 0, 1),
            [BlockLegacyIds::END_STONE, 0] => ItemFactory::getInstance()->get(1114, 0, 1),
            [BlockLegacyIds::NETHERRACK, 0] => ItemFactory::getInstance()->get(1120, 0, 1),
            [BlockLegacyIds::SNOW_BLOCK, 0] => ItemFactory::getInstance()->get(1128, 0, 1),
            [BlockLegacyIds::SAND, 0] => ItemFactory::getInstance()->get(1127, 0, 1),
            [BlockLegacyIds::OBSIDIAN, 0] => ItemFactory::getInstance()->get(1122, 0, 1),
            default => ItemFactory::getInstance()->get(1098, 0, 1)
        };
        $minionItem->setCustomName(TextFormat::RESET . TextFormat::YELLOW . $this->getMinionInformation()->getType()->getTargetName() . ' Minion ' . Utils::getRomanNumeral($this->getMinionInformation()->getLevel()))->setLore(["§r§7Place this minion and it will\n§r§7start generating and mining blocks!\n§r§7Requires an open area to spawn\n§r§7blocks. Minions also work when you are offline!\n\n§r§eType: §b" . $this->getMinionInformation()->getType()->getTargetName() . "\n§r§eLevel: §b" . Utils::getRomanNumeral($this->getMinionInformation()->getLevel()) . "\n§r§eResources Collected: §b" . $this->getMinionInformation()->getResourcesCollected() . ""]);
        $minionItem->addEnchantment($this->fakeEnchant);
        $minionItem->getNamedTag()->setTag("MinionInformation", $this->minionInformation->nbtSerialize());

        $this->getWorld()->dropItem($this->getPosition(), $minionItem);
        $this->close();
    }

    public function getTool(string $tool, bool $isNetheriteTool): ?Item
    {
        $tools = [
            BlockToolType::NONE => $isNetheriteTool ? ItemFactory::getInstance()->get(745) : StringToItemParser::getInstance()->parse($tool . ' Pickaxe'),
            BlockToolType::SHOVEL => $isNetheriteTool ? ItemFactory::getInstance()->get(744) : StringToItemParser::getInstance()->parse($tool . ' Shovel'),
            BlockToolType::PICKAXE => $isNetheriteTool ? ItemFactory::getInstance()->get(745) : StringToItemParser::getInstance()->parse($tool . ' Pickaxe'),
            BlockToolType::AXE => $isNetheriteTool ? ItemFactory::getInstance()->get(746) : StringToItemParser::getInstance()->parse($tool . ' Axe'),
            BlockToolType::HOE => $isNetheriteTool ? ItemFactory::getInstance()->get(747) : StringToItemParser::getInstance()->parse($tool . ' Hoe'),
            BlockToolType::SHEARS => ItemFactory::getInstance()->get(ItemIds::SHEARS),
        ];

        return $tools[$this->getMinionInformation()->getType()->toBlock()->getBreakInfo()->getToolType()];
    }

    public function checkFull(): bool
    {
        if ($this->isInventoryFull()) {
            if ($this->getMinionInformation()->getUpgrade()->isAutoSell()) {
                $this->sellItems();
                return true;
            }
            $this->currentAction = self::ACTION_INVENTORY_FULL;
            return false;
        }
        return true;
    }

    public function getRealDrops(): array
    {
        $block = $this->getMinionInformation()->getType()->toBlock();
        $drops = $block->getDropsForCompatibleTool(VanillaItems::AIR());
        if (count($drops) === 0) {
            $drops = $block->getSilkTouchDrops(VanillaItems::AIR());
        }
        return $drops;
    }

    public function sellItems(): void
    {
        $sellPrices = SellAll::getInstance()->getConfig()->getAll();

        foreach ($this->getMinionInventory()->getContents() as $item) {
            if (isset($sellPrices[$item->getId()])) {
                $this->money += $sellPrices[$item->getId()] * $item->getCount();
                $this->getMinionInventory()->remove($item);
            } elseif (isset($sellPrices[$item->getId() . ':' . $item->getMeta()])) {
                $this->money += $sellPrices[$item->getId() . ':' . $item->getMeta()] * $item->getCount();
                $this->getMinionInventory()->remove($item);
            }
        }
    }

    public function getLevelUpCost(): int
    {
        $costs = (array) BetterMinion::getInstance()->getConfig()->get('levelup-costs');

        return (int) $costs[$this->getMinionInformation()->getLevel()];
    }

    public function canUseAutoSmelt(): bool
    {
        return $this->getMinionInformation()->getLevel() >= MinionUpgrade::AUTO_SMELT_LEVEL;
    }

    public function canUseAutoSell(): bool
    {
        return $this->getMinionInformation()->getLevel() >= MinionUpgrade::AUTO_SELL_LEVEL;
    }

    public function canUseCompacter(): bool
    {
        return $this->getMinionInformation()->getLevel() >= MinionUpgrade::SUPER_COMPACTER_LEVEL;
    }

    public function canUseExpander(): bool
    {
        return $this->getMinionInformation()->getLevel() >= MinionUpgrade::SUPER_EXPANDER_LEVEL;
    }

    public function getMinionRange(): int
    {
        return $this->getMinionInformation()->getUpgrade()->isSuperExpander() ? 3 : 2;
    }

    public function broadcastPlaceBreak(): bool
    {
        return true;
    }

    public function isWorkFast(): bool
    {
        return false;
    }
}
