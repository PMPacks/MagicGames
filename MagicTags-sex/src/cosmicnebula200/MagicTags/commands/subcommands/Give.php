<?php

declare(strict_types=1);

namespace cosmicnebula200\MagicTags\commands\subcommands;

use pocketmine\player\Player;
use pocketmine\item\ItemFactory;
use pocketmine\utils\TextFormat;
use cosmicnebula200\MagicTags\Utils;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use cosmicnebula200\MagicTags\MagicTags;
use CortexPE\Commando\args\RawStringArgument;

class Give extends BaseSubCommand
{
    public function prepare(): void
    {
        $this->setPermission('magictags.give');

        $this->registerArgument(0, new RawStringArgument('tag', false));
        $this->registerArgument(1, new RawStringArgument('player', true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $player = isset($args['player']) ? MagicTags::getInstance()->getServer()->getPlayerByPrefix($args['player']) : null;
        if (!$player instanceof Player) {
            $sender->sendMessage(MagicTags::getInstance()->getMessages()->get('prefix') . " Invalid player");
            return;
        }
        if (!Utils::checkTag($args['tag'])) {
            $sender->sendMessage(MagicTags::getInstance()->getMessages()->get('prefix') . " That tag does not exists");
            return;
        }
        $item = ItemFactory::getInstance()->get(MagicTags::getInstance()->getConfig()->get("item-id"), 0);
        $item->setCustomName(TextFormat::colorize(str_replace("{tag_name}", $args["tag"], MagicTags::getInstance()->getConfig()->get('item-name'))));
        
        $lore = [];
        foreach (MagicTags::getInstance()->getConfig()->get('item-lore') as $l) {
            $lore[] = TextFormat::colorize(str_replace("{tag_name}", $args["tag"], $l));
        }

        $item->setLore($lore);
        $item->getNamedTag()->setString('MagicTags', $args['tag']);
        $player->getInventory()->addItem($item);
        if ($player !== $sender) {
            $player->sendMessage(MagicTags::getInstance()->formatMessage("given-tag", [
                "player" => $player,
                "tag_name" => $args['tag']
            ]));
        }
        $sender->sendMessage(MagicTags::getInstance()->formatMessage("receive-tag", [
            "tag_name" => $args['tag']
        ]));
    }
}
