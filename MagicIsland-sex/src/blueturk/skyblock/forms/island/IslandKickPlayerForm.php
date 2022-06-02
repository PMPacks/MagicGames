<?php

namespace blueturk\skyblock\forms\island;

use pocketmine\Server;
use pocketmine\world\World;
use dktapps\pmforms\MenuForm;
use pocketmine\player\Player;
use blueturk\skyblock\SkyBlock;
use dktapps\pmforms\MenuOption;
use blueturk\skyblock\managers\IslandManager;

class IslandKickPlayerForm extends MenuForm
{
    public function __construct(Player $player)
    {
        $options = [];
        $world = Server::getInstance()->getWorldManager()->getWorldByName($player->getName());
        if ($world instanceof World) {
            foreach ($world->getPlayers() as $worldPlayer) {
                $options[] = new MenuOption($worldPlayer->getName());
            }
        }
        parent::__construct(
            SkyBlock::BT_TITLE . "Kick Player from the Island",
            "\n",
            $options,
            function (Player $player, int $option): void {
                $menuOption = $this->getOption($option);
                if (!$menuOption instanceof MenuOption) {
                    return;
                }
                
                $selectedPlayer = $menuOption->getText();
                IslandManager::islandKickPlayer($player, $selectedPlayer);
            }
        );
    }
}
