<?php

namespace blueturk\skyblock\forms\island;

use dktapps\pmforms\MenuForm;
use pocketmine\player\Player;
use blueturk\skyblock\SkyBlock;
use dktapps\pmforms\MenuOption;
use blueturk\skyblock\managers\IslandManager;

class IslandUnBanPlayerForm extends MenuForm
{
    public function __construct(Player $player)
    {
        $options = [];
        $bannedIslands = SkyBlock::getInstance()->getConfig()->getNested($player->getName() . ".island" . ".banneds");
        if (!is_array($bannedIslands)) {
            $bannedIslands = [];
        }

        foreach ($bannedIslands as $item => $value) {
            $options[] = new MenuOption($value);
        }
        parent::__construct(
            SkyBlock::BT_TITLE . "Remove Player Ban",
            "\n",
            $options,
            function (Player $player, int $option): void {
                $menuOption = $this->getOption($option);
                if (!$menuOption instanceof MenuOption) {
                    return;
                }
                
                $selectedPlayer = $menuOption->getText();
                IslandManager::islandUnBanPlayer($player, $selectedPlayer);
            }
        );
    }
}
