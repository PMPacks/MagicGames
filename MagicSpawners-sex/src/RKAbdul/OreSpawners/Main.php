<?php

declare(strict_types=1);

namespace RKAbdul\OreSpawners;

use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\command\Command;
use DenielWorld\EzTiles\EzTiles;
use pocketmine\item\ItemFactory;
use pocketmine\plugin\PluginBase;
use RKAbdul\OreSpawners\util\Util;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;

class Main extends PluginBase
{
    public const VERSION = 4;

    private array $cfg;

    /**
     * Disables the plugin if config version is below const VERSION.
     *
     * @return void
     */
    public function onEnable(): void
    {
        $this->cfg = $this->getConfig()->getAll();

        if ($this->cfg["version"] < self::VERSION) {
            $this->getLogger()->error("Config Version is outdated! Please delete your current config file!");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this, new Util($this)), $this);
        EzTiles::register($this);
        EzTiles::init();
    }

    /**
     * Checks if the OreSpawner command is run.
     *
     * @param  CommandSender $sender
     * @param  Command $command
     * @param  string $label
     * @param  array $args
     * @return bool
     */
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if ($command->getName() === "orespawner") {
            $typesArray = ["coal", "lapis", "iron", "gold", "diamond", "emerald", "redstone"];
            if (!$sender->hasPermission("orespawner.give")) {
                $sender->sendMessage(TF::RED . "You do not have permission to use this command!");
                return false;
            }

            if (count($args) < 3) {
                $sender->sendMessage(TF::RED . "You must provide some arguments! Usage: /orespawner [ore] [amount] [player]");
                return false;
            }

            $player = $this->getServer()->getPlayerByPrefix($args[2]);
            if (!$player instanceof Player) {
                $sender->sendMessage(TF::RED . "You must provide a valid player!");
                return false;
            }

            if (!in_array(strtolower($args[0]), $typesArray)) {
                $sender->sendMessage(TF::RED . "You must enter a valid ore type!");
                return false;
            }
            if (!is_numeric($args[1])) {
                $sender->sendMessage(TF::RED . "You must provide a valid amount!");
                return false;
            }

            $oreSpawner = $this->createOreSpawner(strtolower($args[0]), (int) $args[1]);
            $player->getInventory()->addItem($oreSpawner);
            return true;
        }
        return false;
    }

    /**
     * Creates OreSpawners from given arguments.
     *
     * @param  string $ore
     * @param  int $amount
     * @return Item
     */
    public function createOreSpawner(string $ore, int $amount): Item
    {
        $ore = lcfirst($ore);

        $genBlock = $this->cfg["ore-generator-blocks"][$ore];
        $genCreated = ItemFactory::getInstance()->get((int) $genBlock, 0, $amount);

        $name = str_replace(["{ore}", "&"], [$ore, "§"], $this->cfg["ore-generators-name"] ?? "&a$ore Ore Generator");
        $genCreated->setCustomName($name);

        $lore = str_replace(["{ore}", "&"], [$ore, "§"], $this->cfg["ore-generators-lore"] ?? "Place it down, and ore blocks will spawn above it");
        $genCreated->setLore([$lore]);

        $genCreated->getNamedTag()->setString("orespawner", "daddy");
        return $genCreated;
    }
}
