<?php

namespace AGTHARN\BankUI;

use pocketmine\plugin\PluginBase;
use AGTHARN\BankUI\task\InterestTask;
use AGTHARN\BankUI\command\BankCommand;
use AGTHARN\BankUI\session\SessionManager;

class Main extends PluginBase
{
    private static Main $instance;
    private SessionManager $sessionManager;

    public array $leaderBoard;

    public static function getInstance(): Main
    {
        return self::$instance;
    }

    public function onEnable(): void
    {
        @mkdir($this->getDataFolder() . "data");

        self::$instance = $this;
        $this->sessionManager = new SessionManager();

        $this->loadLeaderBoard();

        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $this->getServer()->getCommandMap()->register("bankui", new BankCommand($this, "bank", "Opens The BankUI!", ["bankui"]));

        $this->getScheduler()->scheduleRepeatingTask(new InterestTask(), 60 * 60 * 20);
    }

    public function onDisable(): void
    {
        $this->saveLeaderBoard();
    }

    public function loadLeaderBoard(): void
    {
        $this->saveResource("top.json");

        $contents = file_get_contents($this->getDataFolder() . "top.json");
        if (is_string($contents)) {
            $this->leaderBoard = json_decode($contents, true);
        }
    }

    public function saveLeaderBoard(): void
    {
        $encoded = json_encode($this->leaderBoard);
        if (is_string($encoded)) {
            file_put_contents($this->getDataFolder() . "top.json", $encoded);
        }
    }

    public function getSessionManager(): SessionManager
    {
        return $this->sessionManager;
    }
}
