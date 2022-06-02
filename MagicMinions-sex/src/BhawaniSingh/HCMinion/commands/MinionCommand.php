<?php

declare(strict_types=1);

namespace BhawaniSingh\HCMinion\commands;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use BhawaniSingh\HCMinion\commands\subcommands\GiveCommand;
use BhawaniSingh\HCMinion\commands\subcommands\RemoveCommand;

class MinionCommand extends BaseCommand
{
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if ($sender instanceof Player || !$sender->hasPermission($this->getPermission() ?? 'minion.commands')) {
            $sender->sendMessage(TextFormat::RED . "You don't have permission to use this command!");
            return;
        }
        $this->sendUsage();
    }

    protected function prepare(): void
    {
        $this->registerSubCommand(new GiveCommand('give', 'Give you a minion spawner'));
        $this->registerSubCommand(new RemoveCommand('remove', 'Quickly remove minions'));
        $this->setPermission('minion.commands');
        $this->setUsage('/minion give|remove');
    }
}
