<?php

declare(strict_types=1);

namespace cosmicnebula200\MagicTags\commands\subcommands;

use pocketmine\player\Player;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use cosmicnebula200\MagicTags\MagicTags;

class Show extends BaseSubCommand
{

    public function prepare(): void
    {
        $this->setPermission('magictags.show');
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            return;
        }
        
        $tagplayer = MagicTags::getInstance()->getPlayerManager()->getPlayer($sender);

        $tags = implode(", ", $tagplayer->getTags());
        $sender->sendMessage(MagicTags::getInstance()->formatMessage("show-tags", [
            "tag_list" => $tags
        ]));
    }
}
