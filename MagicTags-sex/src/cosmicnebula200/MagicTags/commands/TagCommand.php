<?php

declare(strict_types=1);

namespace cosmicnebula200\MagicTags\commands;

use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use cosmicnebula200\MagicTags\commands\subcommands\Give;
use cosmicnebula200\MagicTags\commands\subcommands\Show;
use cosmicnebula200\MagicTags\commands\subcommands\Select;

class TagCommand extends BaseCommand
{
    public function prepare(): void
    {
        $this->registerSubCommand(new Give('give', 'gives a tag to the player mentioned', ["g"]));
        $this->registerSubCommand(new Select('select', 'select a tag which you already own'));
        $this->registerSubCommand(new Show('show', 'shows the list of all tags owned', ['see']));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        // do none
    }
}
