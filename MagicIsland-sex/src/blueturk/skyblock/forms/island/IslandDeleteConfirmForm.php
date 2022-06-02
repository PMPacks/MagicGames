<?php

namespace blueturk\skyblock\forms\island;

use pocketmine\player\Player;
use dktapps\pmforms\ModalForm;
use blueturk\skyblock\SkyBlock;
use blueturk\skyblock\managers\IslandManager;

class IslandDeleteConfirmForm extends ModalForm
{
    public function __construct()
    {
        parent::__construct(
            SkyBlock::BT_TITLE . "Island Delete",
            "§bDo You Wand To Delete Your Island?",
            function (Player $player, bool $choice): void {
                switch ($choice) {
                    case true:
                        IslandManager::islandRemove($player);
                        break;
                    case false:
                        $player->sendForm(new IslandOptionsForm($player));
                        break;
                }
            },
            "§e»§3 YES§e «",
            "< Back"
        );
    }
}
