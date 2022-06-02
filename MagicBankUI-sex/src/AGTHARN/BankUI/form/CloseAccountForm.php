<?php

namespace AGTHARN\BankUI\form;

use AGTHARN\BankUI\Main;
use pocketmine\player\Player;
use jojoe77777\FormAPI\SimpleForm;

class CloseAccountForm
{
    public static function firstPromptForm(Player $player): SimpleForm
    {
        $playerSession = Main::getInstance()->getSessionManager()->getSession($player);
        $bankName = $playerSession->bankProvider;

        $form = new SimpleForm(function (Player $player, ?int $data = null) {
            if ($data === null) {
                return;
            }

            switch ($data) {
                case 0:
                    $player->sendForm(self::secondPromptForm($player));
                    break;
            }
        });

        $form->setTitle("§6» §r§l" . $bankName . " §r§6«");
        $form->setContent("§cAre you sure you want to close your account? This cannot be undone! You will lose all your money in the bank!");
        $form->addButton("§6» §cYes, Close Account §6«\n§8Click To Close Account", 1, "https://cdn-icons-png.flaticon.com/512/3572/3572255.png");
        $form->addButton("§l§cEXIT\n§r§dClick to close...", 1, "textures/ui/cancel");

        return $form;
    }

    public static function secondPromptForm(Player $player): SimpleForm
    {
        $playerSession = Main::getInstance()->getSessionManager()->getSession($player);
        $bankName = $playerSession->bankProvider;

        $form = new SimpleForm(function (Player $player, ?int $data = null) {
            if ($data === null) {
                return;
            }

            switch ($data) {
                case 0:
                    $player->sendForm(self::thirdPromptForm($player));
                    break;
            }
        });

        $form->setTitle("§6» §r§l" . $bankName . " §r§6«");
        $form->setContent("§cyo fr tho why are you closing your account? you sure you wanna do this?");
        $form->addButton("§6» §cobviously i am §6«\n§8(yo we arent going to recover it)", 1, "https://cdn-icons-png.flaticon.com/512/742/742752.png");
        $form->addButton("§l§cEXIT\n§r§dClick to close...", 1, "textures/ui/cancel");

        return $form;
    }

    public static function thirdPromptForm(Player $player): SimpleForm
    {
        $playerSession = Main::getInstance()->getSessionManager()->getSession($player);
        $bankName = $playerSession->bankProvider;

        $form = new SimpleForm(function (Player $player, ?int $data = null) use ($playerSession) {
            if ($data === null) {
                return;
            }

            switch ($data) {
                case 0:
                    $playerSession->resetData();
                    $playerSession->handleMessage(" §cSo sorry to see you go! Not sure why I detected something else talking to you but anyways... You have successfully closed your account.");
                    break;
            }
        });

        $form->setTitle("§6» §r§l" . $bankName . " §r§6«");
        $form->setContent("§calright fine.... this is the last prompt.");
        $form->addButton("§6» §cyes, i h8 you 2 goodbye §6«\n§8Click To Close Account", 1, "https://cdn-icons-png.flaticon.com/512/260/260222.png");
        $form->addButton("§l§cEXIT\n§r§dClick to close...", 1, "textures/ui/cancel");

        return $form;
    }
}
