<?php

namespace AGTHARN\BankUI\form;

use AGTHARN\BankUI\Main;
use pocketmine\player\Player;
use AGTHARN\BankUI\bank\Banks;
use jojoe77777\FormAPI\SimpleForm;
use onebone\economyapi\EconomyAPI;

class MenuForm
{
    public static function getStartForm(Player $player): SimpleForm
    {
        $playerName = $player->getName();

        $form = new SimpleForm(function (Player $player, ?int $data = null) {
            if ($data === null) {
                return;
            }
            
            if ($data + 1 > count(Banks::BANKS))  {
                return;
            }
            $player->sendForm(self::getViewBankForm($player, Banks::BANKS[$data]));
        });
        $form->setTitle("§6» §r§lMAGIC BANK SERVICES §r§6«");
        $form->setContent("Hi, §b$playerName §f!\n\nWelcome to Magic Bank Services! First, we'd like you to choose a bank which best suits you!");
        foreach (Banks::BANKS as $bankData) {
            $form->addButton("§6» §a" . $bankData["name"] . "\n§8" . $bankData["description"], 1, $bankData["logo"]);
        }
        $form->addButton("§l§cEXIT\n§r§dClick to close...", 1, "textures/ui/cancel");

        return $form;
    }

    public static function getViewBankForm(Player $player, array $bankData): SimpleForm
    {
        $playerName = $player->getName();

        $form = new SimpleForm(function (Player $player, ?int $data = null) use ($bankData) {
            if ($data === null) {
                return;
            }

            switch ($data) {
                case 0:
                    Main::getInstance()->getSessionManager()->getSession($player)->setBank($bankData["name"]);
                    break;
                case 1:
                    $player->sendForm(self::getStartForm($player));
                    break;
            }
        });
        $form->setTitle("§6» §r§l" . $bankData["name"] . " §r§6«");
        $form->setContent("Hi, §b$playerName §f! Here are the details about this bank you may be signing up for!\n\n§bDescription:\n §f" . $bankData["description"] . "\n\n§bApproval Time: §f" . $bankData["approvalSeconds"] . "s\n\n§bInterest Rate: §f" . $bankData["interestRate"] . "%\n§bDeposit Tax: §f$" . $bankData["depositTax"] . "\n§bWithdraw Tax: §f$" . $bankData["withdrawTax"] . "\n§bTransfer Tax: §f$" . $bankData["transferTax"] . "\n\n§bStarting Money: §f$" . $bankData["startingMoney"]);
        $form->addButton("§aJoin", 1, "https://cdn-icons-png.flaticon.com/512/1006/1006555.png");
        $form->addButton("§cBack", 1, "textures/blocks/barrier");
        $form->addButton("§l§cEXIT\n§r§dClick to close...", 1, "textures/ui/cancel");

        return $form;
    }

    public static function getActivatingForm(Player $player): SimpleForm
    {
        $playerSession = Main::getInstance()->getSessionManager()->getSession($player);
        $playerName = $player->getName();

        $bankName = $playerSession->bankProvider;
        $timeRemaining = number_format(($playerSession->bankActivateTime + Banks::getBankData($playerSession->bankProvider)["approvalSeconds"] - time()) / 60);

        $form = new SimpleForm(function (Player $player, ?int $data = null) {
            if ($data === null) {
                return;
            }
        });
        $form->setTitle("§6» §r§l" . $bankName . " §r§6«");
        $form->setContent("Hi, §b$playerName §f!\n\nHere at §b$bankName §f, we guarantee great services to our users! However, your bank application has yet to be approved and will be as soon as possible! Please wait patiently for your bank to be approved!\n\n§6TIME REMAINING: $timeRemaining minutes\n\n§fWe hope you enjoy your stay at $bankName and we thank you for your continuous support!\n\n§b~ §6$bankName §b~");
        $form->addButton("§l§cEXIT\n§r§dClick to close...", 1, "textures/ui/cancel");

        return $form;
    }

    public static function getFrozenForm(Player $player): SimpleForm
    {
        $playerSession = Main::getInstance()->getSessionManager()->getSession($player);
        $playerName = $player->getName();

        $bankName = $playerSession->bankProvider;
        
        $form = new SimpleForm(function (Player $player, ?int $data = null) {
            if ($data === null) {
                return;
            }
        });
        $form->setTitle("§6» §r§l" . $bankName . " §r§6«");
        $form->setContent("Hi, §b$playerName §f!\n\nYour account has been flagged and frozen for suspected illegal activities! Please create a ticket on our Discord server to appeal!\n\n§fWe thank you for using $bankName and for your continuous support!\n\n§b~ §6$bankName §b~");
        $form->addButton("§l§cEXIT\n§r§dClick to close...", 1, "textures/ui/cancel");

        return $form;
    }

    public static function getMenuForm(Player $player): SimpleForm
    {
        $playerSession = Main::getInstance()->getSessionManager()->getSession($player);
        $playerName = $player->getName();

        $coinsAtBank = $playerSession->money;
        $coinsInHand = EconomyAPI::getInstance()->myMoney($player);
        $bankName = $playerSession->bankProvider;
        $bankInterest = Banks::getBankData($playerSession->bankProvider)["interestRate"];

        $form = new SimpleForm(function (Player $player, ?int $data = null) use ($playerSession) {
            if ($data === null) {
                return;
            }

            switch ($data) {
                case 0:
                    $player->sendForm(DepositForm::depositForm($player));
                    break;
                case 1:
                    $player->sendForm(WithdrawForm::withdrawForm($player));
                    break;
                case 2:
                    $player->sendForm(TransferForm::transferForm($player));
                    break;
                case 3:
                    $player->sendForm(NoteForm::noteForm($player));
                    break;
                case 4:
                    $player->sendForm(TransactionForm::transactionForm($player));
                    break;
                case 5:
                    if ($playerSession->lastClosedTime + 7 * 24 * 60 * 60 > time()) {
                        $playerSession->handleMessage(" §cYou must wait at least §f7 days §cbefore closing your account again!");
                        return;
                    }
                    $player->sendForm(CloseAccountForm::firstPromptForm($player));
                    break;
            }
        });
        
        $form->setTitle("§6» §r§l" . $bankName . " §r§6«");
        $form->setContent("Hi, §b$playerName §f!\n\n§fCoins at Bank: §b$$coinsAtBank\n§fCoins in Hand: §b$$coinsInHand\n§fInterest Rate: §b$bankInterest%");
        $form->addButton("§6» §aDeposit Money §6«\n§8Click To Deposit", 1, "https://cdn-icons-png.flaticon.com/128/1041/1041888.png");
        $form->addButton("§6» §aWithdraw Money §6«\n§8Click To Withdraw", 1, "https://cdn-icons-png.flaticon.com/128/2535/2535077.png");
        $form->addButton("§6» §aTransfer Money §6«\n§8Click To Transfer", 1, "https://cdn-icons-png.flaticon.com/128/1790/1790213.png");
        $form->addButton("§6» §aConvert to Notes §6«\n§8Click To Open", 1, "https://cdn-icons-png.flaticon.com/128/1043/1043445.png");
        $form->addButton("§6» §aTransaction Logs §6«\n§8Click To Open", 1, "https://cdn-icons-png.flaticon.com/128/3135/3135679.png");
        $form->addButton("§6» §aClose Account §6«\n§8Click To Close", 1, "https://cdn-icons-png.flaticon.com/512/7158/7158854.png");
        $form->addButton("§l§cEXIT\n§r§dClick to close...", 1, "textures/ui/cancel");
        
        return $form;
    }
}
