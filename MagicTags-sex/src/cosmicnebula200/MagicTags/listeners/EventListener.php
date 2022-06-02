<?php

declare(strict_types=1);

namespace cosmicnebula200\MagicTags\listeners;

use pocketmine\event\Listener;
use cosmicnebula200\MagicTags\Utils;
use pocketmine\block\BlockLegacyIds;
use cosmicnebula200\MagicTags\MagicTags;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerInteractEvent;
use alvin0319\GroupsAPI\event\PlayerGroupsUpdatedEvent;

class EventListener implements Listener
{
    /**
     * Sets tag when PurePerms Group changes
     *
     * @param PlayerGroupsUpdatedEvent $event
     * @priority NORMAL
     * @return void
     */
    /*public function onRankChange(PlayerGroupsUpdatedEvent $event): void
    {
        $nameTag = str_replace("{magic_tag}", Utils::getTagName(MagicTags::getInstance()->getPlayerManager()->getPlayer($event->getPlayer())->getCurrentTag()), $event->getPlayer()->getNameTag());
        $event->getPlayer()->setNameTag($nameTag);
    }*/

    /**
     * adds the tags on join (calls after all the other join events so after pp sets their tag)
     *
     * @param PlayerJoinEvent $event
     * @priority HIGHEST
     * @return void
     */
    public function onJoin(PlayerJoinEvent $event): void
    {
        MagicTags::getInstance()->getPlayerManager()->createPlayer($event->getPlayer());

        $nameTag = str_replace("{magic_tag}", Utils::getTagName(MagicTags::getInstance()->getPlayerManager()->getPlayer($event->getPlayer())->getCurrentTag()), $event->getPlayer()->getNameTag());
        $event->getPlayer()->setNameTag($nameTag);
    }

    /**
     * adds tag on chats
     *
     * @param PlayerChatEvent $event
     * @priority HIGHEST
     * @return void
     */
    public function onChat(PlayerChatEvent $event): void
    {
        $format = strtolower(str_replace("{TAG}", Utils::getTagName(MagicTags::getInstance()->getPlayerManager()->getPlayer($event->getPlayer())->getCurrentTag()), $event->getFormat()));
        $event->setFormat($format);
    }

    /**
     * onInteract
     *
     * @param PlayerInteractEvent $event
     * @return void
     */
    public function onInteract(PlayerInteractEvent $event): void
    {
        if ($event->getItem()->getNamedTag()->getTag("MagicTags") == null || $event->isCancelled()) {
            return;
        }
        if ($event->getBlock()->getId() == BlockLegacyIds::FRAME_BLOCK) {
            $event->cancel();
        }

        $item = $event->getItem();
        $tag = $item->getNamedTag()->getString("MagicTags");
        if (Utils::hasTag($event->getPlayer(), $tag)) {
            return;
        }

        MagicTags::getInstance()->getPlayerManager()->getPlayer($event->getPlayer())->addTag($tag);
        $event->getPlayer()->sendMessage(MagicTags::getInstance()->formatMessage('claimed-tag', [
            "tag" => $tag
        ]));
        $item->setCount($item->getCount() - 1);
        $event->getPlayer()->getInventory()->setItemInHand($item);
    }
}
