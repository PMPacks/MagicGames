<?php

namespace Quest\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Quest\forms\QuestInfoForm;

class QuestCommand extends Command
{

    public function __construct()
    {
        parent::__construct("quest","Quest");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        if (!$sender instanceof Player){
            return false;
        }
        $sender->sendForm(new QuestInfoForm($sender));
        return true;
    }
}