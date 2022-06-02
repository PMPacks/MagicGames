<?php

namespace AGTHARN\BankUI\form;

use AGTHARN\BankUI\Main;
use pocketmine\player\Player;
use AGTHARN\BankUI\bank\Banks;
use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use onebone\economyapi\EconomyAPI;

class DepositForm
{
    public static function depositForm(Player $player): SimpleForm
    {
        $playerSession = Main::getInstance()->getSessionManager()->getSession($player);
        $bankName = $playerSession->bankProvider;

        /** @var float $coinsInHand */
        $coinsInHand = EconomyAPI::getInstance()->myMoney($player);
        
        $form = new SimpleForm(function (Player $player, ?int $data = null) use ($playerSession, $coinsInHand) {
            if ($data === null) {
                return;
            }

            switch ($data) {
                case 0:
                    $playerSession->depositMoney($coinsInHand);
                    break;
                case 1:
                    $playerSession->depositMoney($coinsInHand / 2);
                    break;
                case 2:
                    $player->sendForm(self::depositCustomForm($player));
                    break;
                case 3:
                    $player->sendForm(MenuForm::getMenuForm($player));
                    break;
            }
        });
        
        $form->setTitle("§6» §r§l" . $bankName . " §r§6«");
        $form->setContent("§bCoins in Hand: §e$$coinsInHand");
        $form->addButton("§6» §aDeposit All §6«\n§8Deposit $coinsInHand", 1, "https://cdn-icons-png.flaticon.com/128/1041/1041888.png");
        $form->addButton("§6» §aDeposit Half §6«\n§8Deposit " . ($coinsInHand / 2), 1, "https://cdn-icons-png.flaticon.com/128/1041/1041888.png");
        $form->addButton("§6» §aDeposit Custom §6«\n§8Deposit Any", 1, "https://cdn-icons-png.flaticon.com/128/1041/1041888.png");
        $form->addButton("§cBack", 1, "textures/blocks/barrier");
        
        return $form;
    }

    public static function depositCustomForm(Player $player): CustomForm
    {
        $playerSession = Main::getInstance()->getSessionManager()->getSession($player);
        $coinsInHand = EconomyAPI::getInstance()->myMoney($player);
        $bankName = $playerSession->bankProvider;

        $form = new CustomForm(function (Player $player, ?array $data = null) use ($playerSession) {
            if ($data === null) {
                return;
            }
            $depositTax = Banks::getBankData($playerSession->bankProvider)["depositTax"];

            $player->sendForm(self::confirmDepositForm($player, (float) $data[1], $depositTax));
        });
        
        $form->setTitle("§6» §r§l" . $bankName . " §r§6«");
        $form->addLabel("Coins in Hand: §e$$coinsInHand");
        $form->addInput("Enter amount to deposit", "100000");
        
        return $form;
    }

    public static function confirmDepositForm(Player $player, float $amount, float $depositTax): CustomForm
    {
        $playerSession = Main::getInstance()->getSessionManager()->getSession($player);
        $coinsInHand = EconomyAPI::getInstance()->myMoney($player);
        $bankName = $playerSession->bankProvider;

        $form = new CustomForm(function (Player $player, ?array $data = null) use ($amount, $playerSession) {
            if ($data === null) {
                return;
            }

            $playerSession->depositMoney($amount, $data[1]);
        });
        
        $form->setTitle("§6» §r§l" . $bankName . " §r§6«");
        $form->addLabel("Coins in Hand: §e$$coinsInHand\n§rDeposit Amount: §e$$amount\n§rDeposit Tax: §e$$depositTax\n\n§rAre you sure you want to deposit this amount?");
        $form->addToggle("Amount Include Taxes?", false);
        
        return $form;
    }
}
