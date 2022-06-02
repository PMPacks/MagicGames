<?php

namespace blueturk\skyblock\commands\island;

use pocketmine\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use blueturk\skyblock\managers\IslandManager;
use blueturk\skyblock\forms\island\IslandVisitAllOpenForm;

class VisitCommand extends Command
{
    public function __construct()
    {
        parent::__construct("visit", "§bVisit Player Island", "/visit {player name}");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): mixed
    {
        if ($sender instanceof Player) {
            if (isset($args[0])) {
                IslandManager::islandVisit($sender, $args[0]);
                return true;
            }
            $sender->sendForm(new IslandVisitAllOpenForm());
        }
        return false;
    }
}
