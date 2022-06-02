<?php

declare(strict_types=1);

namespace AndreasHGK\EasyKits\ui;

use AndreasHGK\EasyKits\Kit;
use AndreasHGK\EasyKits\utils\LangUtils;
use AndreasHGK\EasyKits\libs\jojoe77777\FormAPI\SimpleForm;
use pocketmine\data\bedrock\EffectIdMap;
use pocketmine\player\Player;
use pocketmine\Server;

class EditkitPotionSelectForm {

    public static function sendTo(Player $player, Kit $kit) : void {
        $ui = new SimpleForm(function (Player $player, $data) use ($kit) {
            if($data === null) {
                EditkitMainForm::sendTo($player, $kit);
                return;
            }

            EditkitPotionForm::sendTo($player, $kit, $data+1);
        });
        $ui->setTitle(LangUtils::getMessage("editkit-title"));
        $ui->setContent(LangUtils::getMessage("editkit-potionselect-text"));
        $effects = [];
        for($i=1; $i <= 26; $i++) {
            $effects[] = EffectIdMap::getInstance()->fromId($i)->getName();
        }
        foreach($effects as $effect) {
            $ui->addButton(LangUtils::getMessage("editkit-potionselect-button", true, ["{POTION}" => Server::getInstance()->getLanguage()->translate($effect)]));
        }
        $player->sendForm($ui);
    }

}