<?php

declare(strict_types=1);

namespace BhawaniSingh\HCMinion\commands\subcommands;

use pocketmine\Server;
use pocketmine\player\Player;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use BhawaniSingh\HCMinion\BetterMinion;
use CortexPE\Commando\args\RawStringArgument;

class RemoveCommand extends BaseSubCommand
{
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender->hasPermission('minion.commands')) {
            $sender->sendMessage("You don't have permission to use this command!");
            return;
        }

        $player = Server::getInstance()->getPlayerByPrefix($args['player']);
        if (!$player instanceof Player) {
            $sender->sendMessage("That player can't be found");
            return;
        }
        
        if (isset(BetterMinion::getInstance()->isRemove[$player->getName()])) {
            unset(BetterMinion::getInstance()->isRemove[$player->getName()]);
        } else {
            BetterMinion::getInstance()->isRemove[$player->getName()] = $player->getName();
        }
        $sender->sendMessage($player->getName() . ' has ' . (isset(BetterMinion::getInstance()->isRemove[$sender->getName()]) ? 'enter in' : 'no longer in') . ' removable minion mode');
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument('player', false));
    }
}
