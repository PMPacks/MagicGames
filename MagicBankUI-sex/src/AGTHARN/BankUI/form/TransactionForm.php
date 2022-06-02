<?php

namespace AGTHARN\BankUI\form;

use AGTHARN\BankUI\Main;
use pocketmine\player\Player;
use AGTHARN\BankUI\bank\Banks;
use jojoe77777\FormAPI\SimpleForm;

class TransactionForm
{
    public static function transactionForm(Player $player): SimpleForm
    {
        $playerSession = Main::getInstance()->getSessionManager()->getSession($player);
        $bankName = $playerSession->bankProvider;

        $content = "";
        foreach ($playerSession->transactionLogs as $transactionData) {
            $drCr = match ($transactionData["type"]) {
                Banks::TRANSACTION_TYPE_DEPOSIT => "Dr",
                Banks::TRANSACTION_TYPE_WITHDRAW => "Cr",
                Banks::TRANSACTION_TYPE_TRANSFER => "Dr",
                default => ""
            };
            $content .= $transactionData["date"] . " §e- " . $transactionData["type"] . " $" . $transactionData["amount"] . " " . $drCr . "§7\n";
        }

        $form = new SimpleForm(function (Player $player, ?int $data = null) {
            if ($data === null) {
                return;
            }

            switch ($data) {
                case 0:
                    $player->sendForm(MenuForm::getMenuForm($player));
                    break;
            }
        });

        $form->setTitle("§6» §r§l" . $bankName . " §r§6«");
        $form->setContent($content);
        $form->addButton("§cBack", 1, "textures/blocks/barrier");
        $form->addButton("§l§cEXIT\n§r§dClick to close...", 1, "textures/ui/cancel");

        return $form;
    }
}
