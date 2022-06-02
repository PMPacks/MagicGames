<?php

namespace blueturk\skyblock\forms\island;

use pocketmine\player\Player;
use dktapps\pmforms\ModalForm;
use blueturk\skyblock\SkyBlock;
use blueturk\skyblock\managers\IslandManager;

class IslandCreateConfirmForm extends ModalForm
{
    public function __construct(string $type)
    {
        parent::__construct(
            SkyBlock::BT_TITLE . "§bIsland Creation Confirmation",
            "\n§7How about creating an Island and embarking on a new adventure?\n\n§aIsland Type: §b" . $type . "\n",
            function (Player $player, bool $choice) use ($type): void {
                switch ($choice) {
                    case true:
                        $player->sendMessage(SkyBlock::BT_MARK . "bYour island is being created..");

                        IslandManager::islandCreate($player, $type);
                        break;
                    case false:
                        $player->sendForm(new IslandTypeForm());
                        break;
                }
            },
            "Create an Island",
            "< Back"
        );
    }
}
