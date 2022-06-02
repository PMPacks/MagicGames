<?php

namespace Quest\event;

use pocketmine\event\Event;
use pocketmine\player\Player;

class PlayerQuestFinishEvent extends Event
{
    private Player $player;

    private string $quest;

    public function __construct(Player $player, string $quest)
    {
        $this->player = $player;
        $this->quest = $quest;
    }

    public function getQuest(): string
    {
        return $this->quest;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }
}
