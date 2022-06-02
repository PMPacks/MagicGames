<?php

declare(strict_types=1);

namespace cosmicnebula200\MagicTags\commands\subcommands;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use alvin0319\GroupsAPI\GroupsAPI;
use jojoe77777\FormAPI\SimpleForm;
use alvin0319\GroupsAPI\group\Group;
use alvin0319\GroupsAPI\user\Member;
use cosmicnebula200\MagicTags\Utils;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use cosmicnebula200\MagicTags\MagicTags;
use cosmicnebula200\MagicTags\listeners\types\TagChangeEvent;

class Select extends BaseSubCommand
{
    public function prepare(): void
    {
        $this->setPermission('magictags.select');
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            return;
        }
        $form = new SimpleForm(function (Player $player, int $data = null): void {
            if ($data === null || $data == 0) {
                return;
            }

            $keys = array_keys(MagicTags::getInstance()->getTags());
            $data--;
            $tag = MagicTags::getInstance()->getTags()[$keys[$data]];
            if (Utils::hasTag($player, $keys[$data])) {
                $tagPlayer = MagicTags::getInstance()->getPlayerManager()->getPlayer($player);
                $event = new TagChangeEvent($tagPlayer->getCurrentTag(), $keys[$data]);

                $member = GroupsAPI::getInstance()->getMemberManager()->getMember($player->getName());
                if (!$member instanceof Member) {
                    return;
                }
                $group = GroupsAPI::getInstance()->getGroupManager()->getGroup($member->getHighestGroup() ?? GroupsAPI::getInstance()->getDefaultGroups()[0]);
                if (!$group instanceof Group) {
                    return;
                }
                $nametag = GroupsAPI::getInstance()->getNameTagFormat($group);
                
                $tagPlayer->setCurrentTag($keys[$data]);
                $event->call();

                $nametag = str_replace('{TAG}', $tag["Name"], $nametag);
                $player->setNameTag($nametag);
                $player->sendMessage(MagicTags::getInstance()->formatMessage('selected-tag', [
                    "tag_name" => $tag["Name"],
                    "nametag" => $player->getNameTag()
                ]));
            }
        });
        $form->addButton("§l§4Close [X]");

        $rt = MagicTags::getInstance()->getTags();
        $tags = array_keys($rt);
        foreach ($tags as $tag) {
            $owned = "§l§cNot Owned";
            if (Utils::hasTag($sender, $tag))
                $owned = "§l§aOwned";
            $form->addButton($rt[$tag]["Display"] . "\n" . $owned);
        }

        $form->setTitle(TextFormat::colorize(MagicTags::getInstance()->getConfig()->get('form-title')));
        $sender->sendForm($form);
    }
}
