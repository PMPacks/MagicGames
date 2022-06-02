<?php

namespace AGTHARN\BankUI\command;

use AGTHARN\BankUI\Main;
use pocketmine\player\Player;
use AGTHARN\BankUI\form\MenuForm;
use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use AGTHARN\BankUI\command\subcommand\TopSubCommand;

class BankCommand extends BaseCommand
{
    public function prepare(): void
    {
        $this->setPermission('bankui.cmd');

        $this->registerSubCommand(new TopSubCommand('top', 'Check the bank leaderboard!'));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("You must be in-game to run this command!");
            return;
        }
        if (!$this->testPermission($sender)) {
            return;
        }
        $playerSession = Main::getInstance()->getSessionManager()->getSession($sender);

        if (!$playerSession->hasBank()) {
            $sender->sendForm(MenuForm::getStartForm($sender));
            return;
        }
        if (!$playerSession->isBankActivated()) {
            $sender->sendForm(MenuForm::getActivatingForm($sender));
            return;
        }
        if ($playerSession->frozen) {
            $sender->sendForm(MenuForm::getFrozenForm($sender));
            return;
        }
        $sender->sendForm(MenuForm::getMenuForm($sender));
    }
}
