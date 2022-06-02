<?php

namespace Quest\forms;

use dktapps\pmforms\MenuForm;
use pocketmine\player\Player;
use Quest\Quest;
use Quest\utils\Utils;

class QuestInfoForm extends MenuForm
{
    
    public function __construct(Player $player)
    {
        $provider = Quest::getInstance()->getProvider();
        $quest = [];
        if ($provider->hasQuest($player->getName())){

            $questInformation = $provider->getQuestFromPlayer($player->getName());
            $quest = [
                "§6=====================§r",
                "§fQuest Name: §a" . $questInformation["quest"],
                "        ",
                "§fQuest Type: §c" . explode(" ", $questInformation["quest"])[0],
                "       ",
                "§fProgress: §6" . $questInformation["progress"] . "§7/§a" . Quest::getInstance()->getQuest()->get("quests")[$questInformation["quest"]]["item"]["amount"],
                Utils::getProgress((int)$questInformation["progress"], (int)Quest::getInstance()->getQuest()->get("quests")[$questInformation["quest"]]["item"]["amount"]),
                "       ",
                "§fItem: ",
                "  §fId: §a" . Quest::getInstance()->getQuest()->get("quests")[$questInformation["quest"]]["item"]["id"],
                "  §fMeta: §a" . Quest::getInstance()->getQuest()->get("quests")[$questInformation["quest"]]["item"]["meta"],
                "§6====================="
            ];
        }else{
            $quest = [
                "§6=====================§r",
                "§fQuest Name: §aNO Quest",
                "        ",
                "§fQuest Type: §cNO Quest",
                "       ",
                "§fProgress: §6NO Quest",
                "§7[§7]",
                "       ",
                "§fItem: ",
                "  §fId: §aNO Quest",
                "  §fMeta: §aNO Quest",
                "§6====================="
                ];
        }
        parent::__construct("§a» §eRunning Quests §a«", implode("\n", $quest), [], function (): void{});
    }

}