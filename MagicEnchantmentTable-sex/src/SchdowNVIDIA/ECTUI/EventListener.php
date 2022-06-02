<?php

namespace SchdowNVIDIA\ECTUI;

use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\item\Armor;
use pocketmine\block\Block;
use pocketmine\player\Player;
use CLADevs\VanillaX\VanillaX;
use pocketmine\event\Listener;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\BlockLegacyIds;
use pocketmine\world\sound\AnvilUseSound;
use pocketmine\world\sound\AnvilFallSound;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\enchantment\EnchantmentInstance;
use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;

class EventListener implements Listener
{
    private Main $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function generateEnchants(Item $toEnchant, Block $ectable): array
    {
        $bookShelves = $this->plugin->getBookshelves($ectable);
        switch (true) {
            case $bookShelves > 15:
                $levelSub = 1.00;
                break;
            case $bookShelves > 10:
                $levelSub = 0.70;
                break;
            case $bookShelves > 5:
                $levelSub = 0.40;
                break;
            default:
                $levelSub = 0.20;
                break;
        }

        return $this->formEnchants($toEnchant, $levelSub);
    }

    public function formEnchants(Item $toEnchant, float $levelSub): array
    {
        if (!$toEnchant instanceof Tool && !$toEnchant instanceof Armor) {
            return [];
        }

        $enchants = [];
        /** @var Enchantment $enchantment */
        /** @phpstan-ignore-next-line */
        foreach (VanillaX::getInstance()->getEnchantmentManager()->getEnchantmentForItem($toEnchant) as $enchantment) {
            $enchants[] = $enchantment;
        }

        if (count($enchants) < 3) {
            for ($i = 0; $i < (3 - count($enchants)); $i++) {
                $enchants[] = $enchants[array_rand($enchants)];
            }
        }
        $arrayRand = array_rand($enchants, 3);

        /** @var EnchantmentTrait[] $enchants */
        return [
            0 =>  [
                "id" => $enchants[$arrayRand[0]]->getId(),
                "mcpeId" => $enchants[$arrayRand[0]]->getMcpeId(),
                "level" => rand(1, intval($enchants[$arrayRand[0]]->getMaxLevel() * ($levelSub - 0.15))),
                "xp" => rand(intval(2 * ($levelSub + 1)), intval(6 * ($levelSub + 1)))
            ],
            1 =>  [
                "id" => $enchants[$arrayRand[1]]->getId(),
                "mcpeId" => $enchants[$arrayRand[1]]->getMcpeId(),
                "level" => rand(1, intval($enchants[$arrayRand[1]]->getMaxLevel() * ($levelSub - 0.10))),
                "xp" => rand(intval(6 * ($levelSub + 1)), intval(10 * ($levelSub + 1)))
            ],
            2 =>  [
                "id" => $enchants[$arrayRand[2]]->getId(),
                "mcpeId" => $enchants[$arrayRand[2]]->getMcpeId(),
                "level" => rand(2, intval($enchants[$arrayRand[2]]->getMaxLevel() * ($levelSub))),
                "xp" => rand(intval(10 * ($levelSub + 1)), intval(15 * ($levelSub + 1)))
            ]
        ];
    }

    public function openECTUI(Player $player, Item $toEnchant, Block $ectable): void
    {
        $player->getWorld()->addSound($player->getPosition(), new AnvilFallSound());

        $enchants = $this->generateEnchants($toEnchant, $ectable);
        if (count($enchants) === 0) {
            $player->sendMessage("§8(§b!§8) §7There are no enchantments available for this item!");
            return;
        }

        $form = new SimpleForm(function (Player $player, int $data = null) use ($toEnchant, $enchants) {
            if ($data === null) {
                return;
            }
            switch ($data) {
                case 0:
                    if ($player->getXpManager()->getXpLevel() < $enchants[0]["xp"]) {
                        $player->sendMessage("§8(§b!§8) §7You don't have enough levels!");
                        return;
                    }
                    /** @var Enchantment $ench */
                    $ench = EnchantmentIdMap::getInstance()->fromId($enchants[0]["mcpeId"]);
                    if ($toEnchant->getEnchantment($ench) instanceof EnchantmentInstance) {
                        $player->sendMessage("§8(§b!§8) §7You can't enchant the same enchantment again!");
                        return;
                    }
                    $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound());
                    $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - $enchants[0]["xp"]);
                    $level = $enchants[0]["level"];
                    if ($level <= 0) {
                        $level = 1;
                    }

                    if ($toEnchant->getId() !== $player->getInventory()->getItemInHand()->getId()) {
                        $player->sendMessage("§8(§b!§8) §7Are you trying to swindle me?");
                        return;
                    }
                    
                    $toEnchant->addEnchantment(new EnchantmentInstance($ench, (int) $level));
                    $player->getInventory()->setItemInHand($toEnchant);
                    break;
                case 1:
                    if ($player->getXpManager()->getXpLevel() < $enchants[1]["xp"]) {
                        $player->sendMessage("§8(§b!§8) §7You don't have enough levels!");
                        return;
                    }
                    /** @var Enchantment $ench */
                    $ench = EnchantmentIdMap::getInstance()->fromId($enchants[1]["mcpeId"]);
                    if ($toEnchant->getEnchantment($ench) instanceof EnchantmentInstance) {
                        $player->sendMessage("§8(§b!§8) §7You can't enchant the same enchantment again!");
                        return;
                    }
                    $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound());
                    $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - $enchants[1]["xp"]);
                    $level = $enchants[1]["level"];
                    if ($level <= 0) {
                        $level = 1;
                    }

                    if ($toEnchant->getId() !== $player->getInventory()->getItemInHand()->getId()) {
                        $player->sendMessage("§8(§b!§8) §7Are you trying to swindle me?");
                        return;
                    }

                    $toEnchant->addEnchantment(new EnchantmentInstance($ench, (int) $level));
                    $player->getInventory()->setItemInHand($toEnchant);
                    break;
                case 2:
                    if ($player->getXpManager()->getXpLevel() < $enchants[2]["xp"]) {
                        $player->sendMessage("§8(§b!§8) §7You don't have enough levels!");
                        return;
                    }
                    /** @var Enchantment $ench */
                    $ench = EnchantmentIdMap::getInstance()->fromId($enchants[2]["mcpeId"]);
                    if ($toEnchant->getEnchantment($ench) instanceof EnchantmentInstance) {
                        $player->sendMessage("§8(§b!§8) §7You can't enchant the same enchantment again!");
                        return;
                    }
                    $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound());
                    $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - $enchants[2]["xp"]);
                    $level = $enchants[2]["level"];
                    if ($level <= 0) {
                        $level = 1;
                    }

                    if ($toEnchant->getId() !== $player->getInventory()->getItemInHand()->getId()) {
                        $player->sendMessage("§8(§b!§8) §7Are you trying to swindle me?");
                        return;
                    }

                    $toEnchant->addEnchantment(new EnchantmentInstance($ench, (int) $level));
                    $player->getInventory()->setItemInHand($toEnchant);
                    break;
            }
        });

        $form->setTitle("§d§l«§r §bENCHANTING TABLE §d§l»§r§8" . $toEnchant->getName());
        foreach ($enchants as $ec) {
            $lvl = $ec["level"];
            if ($lvl <= 0) {
                $lvl = 1;
            }
            $form->addButton("§d" . $ec["id"] . "§e (" . $lvl . ")§r§a " . $ec["xp"] . " LVL", 1, "https://cdn-icons-png.flaticon.com/128/167/167755.png");
        }
        $form->addButton("§l§cEXIT\n§r§8Tap to exit", 0, "textures/ui/cancel");
        $form->setContent("§bHello §e{$player->getName()}\n\n§bThis Will Enchant The Current Item You Holding In Your Hand");
        $player->sendForm($form);
    }

    public function onTouch(PlayerInteractEvent $event): void
    {
        $block = $event->getBlock();
        if ($block->getId() === BlockLegacyIds::ENCHANTMENT_TABLE) {
            $event->cancel();
            if (!$event->getPlayer()->isSneaking()) {
                $toEnchant = $event->getItem();
                $this->openECTUI($event->getPlayer(), $toEnchant, $block);
            }
        }
    }
}
