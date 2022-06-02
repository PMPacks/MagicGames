<?php

namespace AGTHARN\BankUI\command\subcommand;

use AGTHARN\BankUI\Main;
use pocketmine\player\Player;
use AGTHARN\BankUI\form\MenuForm;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;

class TopSubCommand extends BaseSubCommand
{
    public function prepare(): void
    {
        $this->setPermission('bankui.cmd.top');
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("You must be in-game to run this command!");
            return;
        }
        if (!$this->testPermissionSilent($sender)) {
            return;
        }
        
        foreach (Main::getInstance()->leaderBoard as $name => $money) {
            $sender->sendMessage("§a" . $name . " §7- §e$" . $money . "§7");
        }
    }
}
