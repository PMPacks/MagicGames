<?php

namespace Quest\listener;

use Quest\Quest;
use pocketmine\Server;
use pocketmine\item\Item;
use pocketmine\event\Listener;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use Quest\event\PlayerQuestFinishEvent;
use Quest\event\PlayerQuestChangedEvent;
use Ifera\ScoreHud\event\TagsResolveEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\event\player\PlayerJoinEvent;
use Ifera\ScoreHud\event\PlayerTagUpdateEvent;
use pocketmine\event\inventory\CraftItemEvent;

class QuestListener implements Listener
{

    public function onJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        $quest = Quest::getInstance();
        $provider = $quest->getProvider();
        $questConfig = $quest->getQuest();
        if (!$provider->hasQuest($player->getName())) {
            $randomQuest = array_keys($questConfig->get("quests"));
            $randomQuest  = $randomQuest[array_rand($randomQuest)];
            $provider->addQuest($player->getName(), (string)$randomQuest);
            (new PlayerQuestChangedEvent($player, (string)$randomQuest))->call();
            $player->sendMessage("  §6§lNEW OBJECTIVE\n  §r§f" . $randomQuest);
        }
    }

    public function onBlockBreak(BlockBreakEvent $event): void
    {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $quest = Quest::getInstance();
        $provider = $quest->getProvider();
        $questConfig = $quest->getQuest();
        if ($provider->hasQuest($player->getName()) && explode(" ", $provider->getQuestFromPlayer($player->getName())["quest"])[0] == "Break") {

            $questConfigg = $questConfig->get("quests")[$provider->getQuestFromPlayer($player->getName())["quest"]];
            if ($questConfigg["item"]["id"] === $block->getId() and $questConfigg["item"]["meta"] === $block->getMeta()) {
                if ($provider->getQuestFromPlayer($player->getName())["progress"] + 1 === $questConfig->get("quests")[$provider->getQuestFromPlayer($player->getName())["quest"]]["progress"]) {
                    (new PlayerQuestFinishEvent($player, $provider->getQuestFromPlayer($player->getName())["quest"]))->call();
                    $randomQuest = array_keys($questConfig->get("quests"));
                    $randomQuest = $randomQuest[array_rand($randomQuest)];
                    //if ($randomQuest === $provider->getQuestFromPlayer($player->getName())["quest"]) $randomQuest = array_rand(array_keys($questConfig->get("quests")));
                    $provider->removeQuest($player->getName());
                    $provider->addQuest($player->getName(), (string)$randomQuest);
                    (new PlayerQuestChangedEvent($player, (string)$randomQuest))->call();
                    $player->sendMessage("  §6§lNEW OBJECTIVE\n  §r§f" . $randomQuest);
                } else {
                    $provider->updateQuestFromPlayer($player->getName(), $provider->getQuestFromPlayer($player->getName())["progress"] + 1);
                }
            }
        }
    }

    public function onCraftItem(CraftItemEvent $event): void
    {
        $player = $event->getPlayer();
        $quest = Quest::getInstance();
        $array = $event->getOutputs();

        $item = array_pop($array);
        if (!$item instanceof Item) {
            return;
        }

        $provider = $quest->getProvider();
        $questConfig = $quest->getQuest();
        if ($provider->hasQuest($player->getName()) && explode(" ", $provider->getQuestFromPlayer($player->getName())["quest"])[0] == "Craft") {
            $questConfigg = $questConfig->get("quests")[$provider->getQuestFromPlayer($player->getName())["quest"]];
            if ($questConfigg["item"]["id"] === $item->getId() and $questConfigg["item"]["meta"] === $item->getMeta()) {
                if ($provider->getQuestFromPlayer($player->getName())["progress"] + $item->getCount() >= $questConfig->get("quests")[$provider->getQuestFromPlayer($player->getName())["quest"]]["progress"]) {
                    (new PlayerQuestFinishEvent($player, $provider->getQuestFromPlayer($player->getName())["quest"]))->call();
                    //TODO: Send Message
                    $randomQuest = array_keys($questConfig->get("quests"));
                    $randomQuest = $randomQuest[array_rand($randomQuest)];
                    //if ($randomQuest === $provider->getQuestFromPlayer($player->getName())["quest"]) $randomQuest = array_rand(array_keys($questConfig->get("quests")));
                    $provider->removeQuest($player->getName());
                    $provider->addQuest($player->getName(), (string)$randomQuest);
                    (new PlayerQuestChangedEvent($player, (string)$randomQuest))->call();
                    $player->sendMessage("  §6§lNEW OBJECTIVE\n  §r§f" . $randomQuest);
                } else {
                    $provider->updateQuestFromPlayer($player->getName(), $provider->getQuestFromPlayer($player->getName())["progress"] + $item->getCount());
                }
            }
        }
    }

    public function onPlace(BlockPlaceEvent $event): void
    {
        $player = $event->getPlayer();
        $quest = Quest::getInstance();
        $provider = $quest->getProvider();
        $block = $event->getBlock();
        $questConfig = $quest->getQuest();
        if ($provider->hasQuest($player->getName()) && explode(" ", $provider->getQuestFromPlayer($player->getName())["quest"])[0] == "Place") {
            $questConfigg = $questConfig->get("quests")[$provider->getQuestFromPlayer($player->getName())["quest"]];
            if ($questConfigg["item"]["id"] === $block->getId() and $questConfigg["item"]["meta"] === $block->getMeta()) {
                if ($provider->getQuestFromPlayer($player->getName())["progress"] + 1 === $questConfig->get("quests")[$provider->getQuestFromPlayer($player->getName())["quest"]]["progress"]) {
                    //TODO: Send Message
                    (new PlayerQuestFinishEvent($player, $provider->getQuestFromPlayer($player->getName())["quest"]))->call();
                    $randomQuest = array_keys($questConfig->get("quests"));
                    $randomQuest = $randomQuest[array_rand($randomQuest)];
                    //if ($randomQuest === $provider->getQuestFromPlayer($player->getName())["quest"]) $randomQuest = array_rand(array_keys($questConfig->get("quests")));
                    $provider->removeQuest($player->getName());
                    $provider->addQuest($player->getName(), (string)$randomQuest);
                    (new PlayerQuestChangedEvent($player, (string)$randomQuest))->call();
                    $player->sendMessage("  §6§lNEW OBJECTIVE\n  §r§f" . $randomQuest);
                } else {
                    $provider->updateQuestFromPlayer($player->getName(), $provider->getQuestFromPlayer($player->getName())["progress"] + 1);
                }
            }
        }
    }

    public function onQuestFinish(PlayerQuestFinishEvent $event): void
    {
        $player = $event->getPlayer();
        $quest = $event->getQuest();
        foreach (Quest::getInstance()->getQuest()->get("quests")[$quest]["rewardCommands"] as $command) {
            Server::getInstance()->getCommandMap()->dispatch(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), str_replace(
                [
                    "{player}",
                    "{name}"
                ],
                [
                    $player,
                    $player->getDisplayName()
                ],
                $command
            ));
            //TODO: Send Message
        }
    }

    public function onQuestChanged(PlayerQuestChangedEvent $event): void
    {
        $player = $event->getPlayer();
        $newQuest = $event->getNewQuest();
        if ($player->isOnline()) {
            (new PlayerTagUpdateEvent($player, new ScoreTag("quest.quest", $newQuest)))->call();
        }
    }

    public function onTagResolve(TagsResolveEvent $event): void
    {
        $player = $event->getPlayer();
        $tag = $event->getTag();
        $provider = Quest::getInstance()->getProvider();
        if ($tag->getName() === "quest.quest") {
            if (count($provider->getQuestFromPlayer($player->getName())) === 0) {
                $tag->setValue("No Quest");
            } else {
                $tag->setValue($provider->getQuestFromPlayer($player->getName())["quest"]);
            }
        }
    }
}
