<?php

namespace AGTHARN\BankUI\session\type;

use AGTHARN\BankUI\Main;
use AGTHARN\BankUI\session\Session;

class OfflineSession extends Session
{
    public function __construct(string $playerName)
    {
        $this->name = $playerName;

        $this->fileName = Main::getInstance()->getDataFolder() . "data/" . $this->name . ".json";
        $this->loadData();
    }
}
