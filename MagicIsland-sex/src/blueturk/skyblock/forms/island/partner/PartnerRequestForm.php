<?php

namespace blueturk\skyblock\forms\island\partner;

use pocketmine\player\Player;
use dktapps\pmforms\ModalForm;
use blueturk\skyblock\SkyBlock;
use blueturk\skyblock\managers\IslandManager;

class PartnerRequestForm extends ModalForm
{

    public function __construct(Player $requestPlayer)
    {
        parent::__construct(
            SkyBlock::BT_TITLE . "Partnership Request",
            $requestPlayer->getName() . "The player wants to add you to his/her island as a partner!",
            function (Player $player, bool $choice) use ($requestPlayer): void {
                switch ($choice) {
                    case true:
                        IslandManager::partnerRequestConfirm($player, $requestPlayer->getName());
                        break;
                    case false:
                        $player->sendMessage(SkyBlock::BT_MARK . "bYou did not accept the partner offer!");
                        if ($requestPlayer->isOnline()) {
                            $requestPlayer->sendMessage(SkyBlock::BT_MARK . "bPartnership did not accept your offer!");
                        }
                        break;
                }
            },
            "Admit it",
            "reject"
        );
    }
}
