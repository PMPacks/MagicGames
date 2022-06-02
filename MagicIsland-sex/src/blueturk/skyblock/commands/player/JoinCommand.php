<?php

namespace blueturk\skyblock\commands\player;

use pocketmine\player\Player;
use blueturk\skyblock\SkyBlock;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use blueturk\skyblock\managers\IslandManager;

class JoinCommand extends Command
{
    public function __construct()
    {
        parent::__construct("join", "§bJoin Your Skyblock Island!", "/join", [""]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): mixed
    {
        if ($sender instanceof Player) {
            $data = SkyBlock::getInstance()->getConfig();
            if ($data->getNested($sender->getName() . "." . "island") !== null) {
                IslandManager::teleportToIsland($sender);
                return true;
            }
            $sender->sendMessage(SkyBlock::BT_MARK . "bYou Don't Have An Island, §eCreate An Island With /is");
        }
        return false;
    }
}
