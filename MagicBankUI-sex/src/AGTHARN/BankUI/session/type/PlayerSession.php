<?php

namespace AGTHARN\BankUI\session\type;

use AGTHARN\BankUI\Main;
use pocketmine\player\Player;
use AGTHARN\BankUI\session\Session;

class PlayerSession extends Session
{
    public function __construct(Player $player)
    {
        $this->player = $player;
        $this->name = $player->getName();

        $this->fileName = Main::getInstance()->getDataFolder() . "data/" . $this->name . ".json";
        $this->loadData();
    }
}
