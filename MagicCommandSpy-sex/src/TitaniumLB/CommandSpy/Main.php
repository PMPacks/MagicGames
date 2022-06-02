<?php

namespace TitaniumLB\CommandSpy;

use pocketmine\player\Player;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;

class Main extends PluginBase
{
    public array $snoopers = [];
    public array $protectedPlayers = [];
    public array $protectedCommands = [];

    public function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if (strtolower($command->getName()) == "commandspy" || strtolower($command->getName()) == "cspy") {
            if ($sender instanceof Player) {
                if ($sender->hasPermission("commandspy.cmd")) {
                    if (!isset($this->snoopers[$sender->getUniqueId()->toString()])) {
                        $sender->sendMessage("§aSpy-Mode activated.");
                        $this->snoopers[$sender->getUniqueId()->toString()] = $sender;
                        return true;
                    }
                    $sender->sendMessage("§cSpy-mode deactivated.");
                    unset($this->snoopers[$sender->getUniqueId()->toString()]);
                    return true;
                }
                $sender->sendMessage("§cYou don't have permission to use this command!");
                return false;
            }
        }
        return false;
    }
}
