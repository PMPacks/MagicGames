<?php

namespace blueturk\skyblock\commands\player;

use pocketmine\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use blueturk\skyblock\forms\island\WeatherSettingsForm;

class WeatherCommand extends Command
{
    public function __construct()
    {
        parent::__construct("weather", "§bChange the weather of the island!", "/weather", ["weather forecast"]);
        $this->setPermission("weather.command.bt");
        $this->setPermissionMessage("§8» §7This command is only for VIP users!");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): mixed
    {
        if ($sender instanceof Player) {
            if ($sender->hasPermission("weather.command.bt")) {
                $sender->sendForm(new WeatherSettingsForm());
                return true;
            }
            $sender->sendMessage($this->getPermissionMessage() ?? "§8» §7This command is only for VIP users!");
        }
        return false;
    }
}
