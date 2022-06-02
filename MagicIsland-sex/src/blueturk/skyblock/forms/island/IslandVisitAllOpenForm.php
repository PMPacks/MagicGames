<?php

namespace blueturk\skyblock\forms\island;

use pocketmine\Server;
use dktapps\pmforms\MenuForm;
use pocketmine\player\Player;
use blueturk\skyblock\SkyBlock;
use dktapps\pmforms\MenuOption;
use blueturk\skyblock\managers\IslandManager;

class IslandVisitAllOpenForm extends MenuForm
{
    public function __construct()
    {
        $options = [];
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            $value = SkyBlock::getInstance()->getConfig()->getNested("Visits." . $player->getName());
            if ($value) {
                $options[] = new MenuOption($player->getName());
            }
        }

        parent::__construct(
            SkyBlock::BT_TITLE . "Players Open to Visit",
            "\n",
            $options,
            function (Player $player, int $option): void {
                $menuOption = $this->getOption($option);
                if (!$menuOption instanceof MenuOption) {
                    return;
                }

                $selectedPlayer = $menuOption->getText();
                IslandManager::islandVisit($player, $selectedPlayer);
            }
        );
    }
}
