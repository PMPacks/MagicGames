<?php

namespace blueturk\skyblock\forms\island\partner;

use dktapps\pmforms\MenuForm;
use pocketmine\player\Player;
use blueturk\skyblock\SkyBlock;
use dktapps\pmforms\MenuOption;
use blueturk\skyblock\managers\IslandManager;

class PartnerRemoveForm extends MenuForm
{
    public function __construct(Player $player)
    {
        $options = [];
        $partners = SkyBlock::getInstance()->getConfig()->getNested($player->getName() . ".island" . ".this-partners");
        if (!is_array($partners)) {
            $partners = [];
        }

        foreach ($partners as $item => $value) {
            $options[] = new MenuOption($value);
        }
        parent::__construct(
            SkyBlock::BT_TITLE . "Remove Partner",
            "\n",
            $options,
            function (Player $player, int $option): void {
                $menuOption = $this->getOption($option);
                if (!$menuOption instanceof MenuOption) {
                    return;
                }
                
                $selectedPlayer = $menuOption->getText();
                IslandManager::partnerRemove($player, $selectedPlayer);
            }
        );
    }
}
