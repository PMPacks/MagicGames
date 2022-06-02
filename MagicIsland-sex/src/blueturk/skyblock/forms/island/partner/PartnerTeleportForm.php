<?php

namespace blueturk\skyblock\forms\island\partner;

use dktapps\pmforms\MenuForm;
use pocketmine\player\Player;
use blueturk\skyblock\SkyBlock;
use dktapps\pmforms\MenuOption;
use blueturk\skyblock\managers\IslandManager;

class PartnerTeleportForm extends MenuForm
{
    public function __construct(Player $player)
    {
        $options = [];
        if (SkyBlock::getInstance()->getConfig()->getNested($player->getName()) !== null) {
            if (SkyBlock::getInstance()->getConfig()->getNested($player->getName() . ".island") === null) {
                if (SkyBlock::getInstance()->getConfig()->getNested($player->getName() . ".partners") != null) {
                    foreach (SkyBlock::getInstance()->getConfig()->getNested($player->getName() . ".partners") as $item => $value) {
                        $options[] = new MenuOption($value);
                    }
                }
            } else {
                if (SkyBlock::getInstance()->getConfig()->getNested($player->getName() . ".island" . ".other-partners") != null) {
                    foreach (SkyBlock::getInstance()->getConfig()->getNested($player->getName() . ".island" . ".other-partners") as $item => $value) {
                        $options[] = new MenuOption($value);
                    }
                }
            }
        }
        parent::__construct(
            SkyBlock::BT_TITLE . "Teleport to Partner Island",
            "§7Choose the partner you want to teleport to!",
            $options,
            function (Player $player, int $option): void {
                $menuOption = $this->getOption($option);
                if (!$menuOption instanceof MenuOption) {
                    return;
                }
                
                $selectedPlayer = $menuOption->getText();
                IslandManager::teleportPartnerIsland($player, $selectedPlayer);
            }
        );
    }
}
